'use strict';

//cart-box__wrap

const CartAbstract = function () {
    this.cookieName = 'BITRIX_SM_CART_PRODUCT_CUSTOM';
    this.items = [];
    this.cartCountInstance = $('.cart-btn__count');
    this.AddItem = function (id) {
        if (this.itemInCart(id)) return;
        this.setItemsFromCookie();
        this.items.push(id);
        this.transferItemsToCookies();
        this.renderCartCount();
    };
    this.renderCartCount = function () {
        $(this.cartCountInstance).text(this.getItemsCount());
    };
    this.itemInCart = function (foundedId) {
        this.setItemsFromCookie();
        return this.items.find(id => id === foundedId) !== undefined;
    };
    this.getItemsFromCookie = function () {
        const itemsInBase64 = Cookies.get(this.cookieName);
        return JSON.parse($.base64.decode(itemsInBase64));
    };
    this.transferItemsToCookies = function () {
        const itemsInBase64 = $.base64.encode(JSON.stringify(this.items));
        Cookies.set(this.cookieName, itemsInBase64);
    };
    this.deleteItem = function (id) {
        this.setItemsFromCookie();
        this.items = this.items.filter(itemId => id !== itemId);
        this.transferItemsToCookies();
        this.renderCartCount();
    };
    this.setItemsFromCookie = function () {
        this.items = this.getItemsFromCookie();
        this.renderCartCount();
    };
    this.getItemsCount = function () {
        return this.items.length
    };
    this.clearAllItems = function () {
        this.items = [];
        this.transferItemsToCookies()
    };
    this.setCookie = function () {
        if (Cookies.get(this.cookieName) === undefined) {
            this.transferItemsToCookies();
        }
    }
};

const cart = new CartAbstract();
cart.setCookie();
cart.setItemsFromCookie();

setInterval(() => {
    cart.setItemsFromCookie();
}, 1000);

