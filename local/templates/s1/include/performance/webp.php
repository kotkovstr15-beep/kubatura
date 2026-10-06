<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** Registers one final-output pass that replaces local JPEG/PNG image URLs with WebP cache URLs. */
function kubaturaRegisterWebpOutputHandler()
{
    static $registered = false;

    if ($registered || defined('ADMIN_SECTION')) {
        return;
    }

    $registered = true;
    AddEventHandler('main', 'OnEndBufferContent', 'kubaturaRewriteHtmlImagesToWebp');
}

/**
 * Creates a WebP copy in /upload/webp-cache and returns its public URL.
 * Returns the original URL when the source is external, unsupported or the
 * server has no GD WebP support/write permissions.
 */
function kubaturaWebpUrl($sourceUrl, $quality = 82)
{
    static $memo = array();

    $sourceUrl = html_entity_decode(trim((string)$sourceUrl), ENT_QUOTES, 'UTF-8');
    if ($sourceUrl === '' || strpos($sourceUrl, 'data:') === 0 || strpos($sourceUrl, 'blob:') === 0) {
        return $sourceUrl;
    }

    $memoKey = $sourceUrl . '|' . (int)$quality;
    if (isset($memo[$memoKey])) {
        return $memo[$memoKey];
    }

    if (!function_exists('imagewebp') || !function_exists('getimagesize')) {
        return $memo[$memoKey] = $sourceUrl;
    }

    $parts = @parse_url($sourceUrl);
    if ($parts === false || empty($parts['path'])) {
        return $memo[$memoKey] = $sourceUrl;
    }

    if (!empty($parts['host'])) {
        $currentHost = isset($_SERVER['HTTP_HOST']) ? preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']) : '';
        if (strcasecmp($parts['host'], $currentHost) !== 0) {
            return $memo[$memoKey] = $sourceUrl;
        }
    }

    $extension = strtolower(pathinfo($parts['path'], PATHINFO_EXTENSION));
    if ($extension !== 'jpg' && $extension !== 'jpeg' && $extension !== 'png') {
        return $memo[$memoKey] = $sourceUrl;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    if (!$documentRoot) {
        return $memo[$memoKey] = $sourceUrl;
    }

    $relativePath = rawurldecode($parts['path']);
    $sourceFile = realpath($documentRoot . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR));
    if (!$sourceFile || !is_file($sourceFile) || strpos($sourceFile, $documentRoot . DIRECTORY_SEPARATOR) !== 0) {
        return $memo[$memoKey] = $sourceUrl;
    }

    $quality = max(50, min(95, (int)$quality));
    $cacheKey = sha1(str_replace('\\', '/', $sourceFile) . '|' . $quality);
    $cacheRelativeDirectory = '/upload/webp-cache/' . substr($cacheKey, 0, 2);
    $cacheDirectory = $documentRoot . str_replace('/', DIRECTORY_SEPARATOR, $cacheRelativeDirectory);
    $cacheFile = $cacheDirectory . DIRECTORY_SEPARATOR . $cacheKey . '.webp';
    $cacheUrl = $cacheRelativeDirectory . '/' . $cacheKey . '.webp';

    if (is_file($cacheFile) && filemtime($cacheFile) >= filemtime($sourceFile)) {
        return $memo[$memoKey] = $cacheUrl;
    }

    if (!is_dir($cacheDirectory) && !@mkdir($cacheDirectory, 0755, true) && !is_dir($cacheDirectory)) {
        return $memo[$memoKey] = $sourceUrl;
    }

    $imageInfo = @getimagesize($sourceFile);
    if (!$imageInfo || empty($imageInfo[2])) {
        return $memo[$memoKey] = $sourceUrl;
    }

    // Avoid exhausting PHP memory on unexpectedly large originals.
    if ((int)$imageInfo[0] * (int)$imageInfo[1] > 25000000) {
        return $memo[$memoKey] = $sourceUrl;
    }

    if ($imageInfo[2] === IMAGETYPE_JPEG && function_exists('imagecreatefromjpeg')) {
        $image = @imagecreatefromjpeg($sourceFile);
    } elseif ($imageInfo[2] === IMAGETYPE_PNG && function_exists('imagecreatefrompng')) {
        $image = @imagecreatefrompng($sourceFile);
        if ($image) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }
    } else {
        $image = false;
    }

    if (!$image) {
        return $memo[$memoKey] = $sourceUrl;
    }

    $temporaryFile = $cacheFile . '.' . getmypid() . '.tmp';
    $written = @imagewebp($image, $temporaryFile, $quality);
    imagedestroy($image);

    if (!$written || !is_file($temporaryFile)) {
        @unlink($temporaryFile);
        return $memo[$memoKey] = $sourceUrl;
    }

    if (is_file($cacheFile)) {
        @unlink($cacheFile);
    }

    if (!@rename($temporaryFile, $cacheFile)) {
        @unlink($temporaryFile);
        return $memo[$memoKey] = $sourceUrl;
    }

    @chmod($cacheFile, 0644);

    return $memo[$memoKey] = $cacheUrl;
}

/** Resizes a Bitrix file first, then stores and returns its WebP representation. */
function kubaturaPreparedImage($picture, $maxWidth, $maxHeight, $quality = 82, $resizeType = null)
{
    if (!is_array($picture)) {
        $picture = array();
    }

    $prepared = array(
        'src' => isset($picture['SRC']) ? $picture['SRC'] : (isset($picture['src']) ? $picture['src'] : ''),
        'width' => isset($picture['WIDTH']) ? (int)$picture['WIDTH'] : (isset($picture['width']) ? (int)$picture['width'] : 0),
        'height' => isset($picture['HEIGHT']) ? (int)$picture['HEIGHT'] : (isset($picture['height']) ? (int)$picture['height'] : 0),
    );

    $fileId = isset($picture['ID']) ? (int)$picture['ID'] : 0;
    if ($fileId > 0 && class_exists('CFile')) {
        if ($resizeType === null) {
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
        }

        $resized = \CFile::ResizeImageGet(
            $fileId,
            array('width' => (int)$maxWidth, 'height' => (int)$maxHeight),
            $resizeType,
            false,
            array(),
            false,
            (int)$quality
        );
        if (!empty($resized['src'])) {
            $prepared = array(
                'src' => $resized['src'],
                'width' => isset($resized['width']) ? (int)$resized['width'] : 0,
                'height' => isset($resized['height']) ? (int)$resized['height'] : 0,
            );
        }
    }

    if ($prepared['src'] !== '') {
        $prepared['src'] = kubaturaWebpUrl($prepared['src'], $quality);

        if ($prepared['width'] <= 0 || $prepared['height'] <= 0) {
            $dimensions = kubaturaLocalImageDimensions($prepared['src']);
            if ($dimensions) {
                $prepared['width'] = $dimensions['width'];
                $prepared['height'] = $dimensions['height'];
            } elseif ($resizeType === BX_RESIZE_IMAGE_EXACT) {
                // Exact Bitrix resize has a known intrinsic ratio even when an
                // older ResizeImageGet implementation omits width/height keys.
                $prepared['width'] = (int)$maxWidth;
                $prepared['height'] = (int)$maxHeight;
            }
        }
    }

    return $prepared;
}

/** Returns intrinsic dimensions for a local image URL, when the file is readable. */
function kubaturaLocalImageDimensions($sourceUrl)
{
    static $memo = array();

    $sourceUrl = html_entity_decode(trim((string)$sourceUrl), ENT_QUOTES, 'UTF-8');
    if ($sourceUrl === '' || isset($memo[$sourceUrl])) {
        return isset($memo[$sourceUrl]) ? $memo[$sourceUrl] : false;
    }

    $parts = @parse_url($sourceUrl);
    if ($parts === false || empty($parts['path']) || !empty($parts['host'])) {
        return $memo[$sourceUrl] = false;
    }

    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    if (!$documentRoot) {
        return $memo[$sourceUrl] = false;
    }

    $relativePath = rawurldecode($parts['path']);
    $imageFile = realpath($documentRoot . DIRECTORY_SEPARATOR . ltrim(str_replace('/', DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR));
    if (!$imageFile || !is_file($imageFile) || strpos($imageFile, $documentRoot . DIRECTORY_SEPARATOR) !== 0) {
        return $memo[$sourceUrl] = false;
    }

    $imageInfo = @getimagesize($imageFile);
    if (!$imageInfo || empty($imageInfo[0]) || empty($imageInfo[1])) {
        return $memo[$sourceUrl] = false;
    }

    return $memo[$sourceUrl] = array('width' => (int)$imageInfo[0], 'height' => (int)$imageInfo[1]);
}

/** Rewrites src/srcset/data-src URLs only inside actual img tags. */
function kubaturaRewriteHtmlImagesToWebp(&$content)
{
    if (!is_string($content) || stripos($content, '<img') === false || stripos($content, '</html>') === false) {
        return;
    }

    $content = preg_replace_callback('#<img\b[^>]*>#i', function ($imageMatch) {
        $tag = $imageMatch[0];
        $quality = stripos($tag, 'news-item__image') !== false ? 78 : 82;
        $isSiteLogo = preg_match('#\balt\s*=\s*(["\'])Кубатура, квартал дизайна и интерьера\1#iu', $tag)
            || preg_match('#\bsrc\s*=\s*(["\'])[^"\']*/local/templates/s1/img/logo\.(?:png|jpe?g|webp)(?:\?[^"\']*)?\1#i', $tag);

        if ($isSiteLogo && !preg_match('#\balt\s*=#i', $tag)) {
            $tag = preg_replace(
                '#\s*/?>$#',
                ' alt="Кубатура, квартал дизайна и интерьера"$0',
                $tag,
                1
            );
        }

        $tag = preg_replace_callback(
            '#\b(src|data-src|data-lazy-src)\s*=\s*(["\'])([^"\']+)\2#i',
            function ($attributeMatch) use ($quality) {
                $optimizedUrl = kubaturaWebpUrl($attributeMatch[3], $quality);
                return $attributeMatch[1] . '=' . $attributeMatch[2] . htmlspecialcharsbx($optimizedUrl) . $attributeMatch[2];
            },
            $tag
        );

        $tag = preg_replace_callback(
            '#\bsrcset\s*=\s*(["\'])([^"\']+)\1#i',
            function ($srcsetMatch) use ($quality) {
                $candidates = explode(',', html_entity_decode($srcsetMatch[2], ENT_QUOTES, 'UTF-8'));
                foreach ($candidates as &$candidate) {
                    $candidate = trim($candidate);
                    if (preg_match('#^(\S+)(\s+.+)?$#', $candidate, $parts)) {
                        $candidate = kubaturaWebpUrl($parts[1], $quality) . (isset($parts[2]) ? $parts[2] : '');
                    }
                }
                unset($candidate);

                return 'srcset=' . $srcsetMatch[1] . htmlspecialcharsbx(implode(', ', $candidates)) . $srcsetMatch[1];
            },
            $tag
        );

        if (!preg_match('#\bsrc\s*=\s*(["\'])([^"\']+)\1#i', $tag, $srcMatch)) {
            return $tag;
        }

        $dimensions = kubaturaLocalImageDimensions($srcMatch[2]);
        if ($dimensions) {
            $missingWidth = !preg_match('#\bwidth\s*=#i', $tag);
            $missingHeight = !preg_match('#\bheight\s*=#i', $tag);
            if ($missingWidth || $missingHeight) {
                $attributes = ($missingWidth ? ' width="' . $dimensions['width'] . '"' : '')
                    . ($missingHeight ? ' height="' . $dimensions['height'] . '"' : '');
                $tag = preg_replace('#\s*/?>$#', $attributes . '$0', $tag, 1);
            }
        }

        // Every local raster image without an explicit loading policy is below
        // the first-screen exceptions, which are marked loading="eager" in
        // their templates. This also covers images from editable include areas.
        $sourcePath = parse_url(html_entity_decode($srcMatch[2], ENT_QUOTES, 'UTF-8'), PHP_URL_PATH);
        $isLocalRaster = is_string($sourcePath)
            && preg_match('#\.(?:avif|jpe?g|png|webp)$#i', $sourcePath)
            && !preg_match('#^https?://#i', $srcMatch[2]);
        if ($isSiteLogo) {
            if (preg_match('#\bloading\s*=#i', $tag)) {
                $tag = preg_replace('#\bloading\s*=\s*(["\'])[^"\']*\1#i', 'loading="eager"', $tag, 1);
            } else {
                $tag = preg_replace('#\s*/?>$#', ' loading="eager"$0', $tag, 1);
            }
        } elseif ($isLocalRaster && !preg_match('#\bloading\s*=#i', $tag)) {
            $tag = preg_replace('#\s*/?>$#', ' loading="lazy" decoding="async"$0', $tag, 1);
        }

        return $tag;
    }, $content);

    // On the home page the above-the-fold layout is covered by critical CSS.
    // Make Bitrix-generated component CSS non-render-blocking as well.
    if (class_exists('CSite') && \CSite::InDir('/index.php')) {
        $content = preg_replace_callback('#<link\b[^>]*\brel\s*=\s*(["\'])stylesheet\1[^>]*>#i', function ($linkMatch) {
            $tag = $linkMatch[0];
            if (stripos($tag, '/bitrix/cache/css/') === false || stripos($tag, 'media=') !== false) {
                return $tag;
            }

            $asyncTag = preg_replace('#\s*/?>$#', ' media="print" onload="this.onload=null;this.media=\'all\'"$0', $tag, 1);
            $fallbackTag = preg_replace('#\s+media="print"\s+onload="[^"]*"#i', '', $asyncTag);
            return $asyncTag . '<noscript>' . $fallbackTag . '</noscript>';
        }, $content);
    }
}
