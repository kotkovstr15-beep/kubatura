<?php
global $USER;
if($USER->IsAuthorized()){
    header("Location: /personal",TRUE,301);
}