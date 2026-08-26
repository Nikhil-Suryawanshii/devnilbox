import axios from "axios";
import { defineStore } from "pinia";
import { useToast } from "vue-toastification";
import AddToCartDialog from "../components/AddCartPopupDialog.vue";
import RemoveCartPopupDialog from "../components/RemoveCartPopupDialog.vue";

import { useAuth } from "./AuthStore";
import { useMaster } from "./MasterStore";

const toast = useToast();

const normalizeId = (id) => Number(id);

export const useBasketStore = defineStore("basketStore", {
    state: () => ({
        total: 0,
        products: [],
        checkoutProducts: [],
        selectedShopIds: [],
        selectedCartIds: [],
        total_amount: 0,
        delivery_charge: 0,
        coupon_discount: 0,
        payable_amount: 0,
        order_tax_amount: 0,
        coupon_code: "",
        all_vat_taxes: [],
        showOrderConfirmModal: false,
        orderPaymentCancelModal: false,
        address: null,
        buyNowShopId: null,
        buyNowProduct: null,
        isLoadingCart: false,
    }),

    getters: {
        totalAmount: (state) => {
            let total = 0;
            state.products.forEach((item) => {
                item.products.forEach((product) => {
                    let price =
                        product.discount_price > 0
                            ? product.discount_price
                            : product.price;
                    total += price * product.quantity;
                });
            });
            return total.toFixed(2);
        },

        totalCheckoutAmount: (state) => {
            let total = 0;
            state.checkoutProducts.forEach((item) => {
                item.products.forEach((product) => {
                    let price =
                        product.discount_price > 0
                            ? product.discount_price
                            : product.price;
                    total += price * product.quantity;
                });
            });
            return total.toFixed(2);
        },

        checkoutTotalItems: (state) => {
            let total = 0;
            state.checkoutProducts.forEach((item) => {
                total += item.products.length;
            });
            return total;
        },

        hasSelectedItems: (state) => state.selectedCartIds.length > 0,
    },

    actions: {
        getAllCartIds() {
            const ids = [];
            this.products.forEach((shop) => {
                shop.products.forEach((product) => {
                    if (product.cart_id != null) {
                        ids.push(normalizeId(product.cart_id));
                    }
                });
            });
            return ids;
        },

        getSelectedShopIds() {
            const shopIds = new Set();
            this.products.forEach((shop) => {
                shop.products.forEach((product) => {
                    if (
                        product.cart_id != null &&
                        this.selectedCartIds
                            .map(normalizeId)
                            .includes(normalizeId(product.cart_id))
                    ) {
                        shopIds.add(normalizeId(shop.shop_id));
                    }
                });
            });
            return [...shopIds];
        },

        syncSelectedShopIds() {
            this.selectedShopIds = this.getSelectedShopIds();
        },

        pruneInvalidCartSelections() {
            const validIds = this.getAllCartIds();
            this.selectedCartIds = this.selectedCartIds
                .map(normalizeId)
                .filter((id) => validIds.includes(id));
            this.syncSelectedShopIds();
        },

        selectAllCartItems() {
            this.selectedCartIds = this.getAllCartIds();
            this.syncSelectedShopIds();
        },

        selectNewCartItems(previousIds) {
            const currentIds = this.getAllCartIds();
            currentIds
                .filter((id) => !previousIds.includes(id))
                .forEach((id) => {
                    if (!this.selectedCartIds.map(normalizeId).includes(id)) {
                        this.selectedCartIds.push(id);
                    }
                });
            this.syncSelectedShopIds();
        },

        onCartItemsUpdated(previousCartIds = null) {
            if (previousCartIds) {
                this.selectNewCartItems(previousCartIds);
            }
            this.pruneInvalidCartSelections();
            if (this.selectedCartIds.length === 0 && this.getAllCartIds().length > 0) {
                if (this.selectedShopIds.length > 0) {
                    this.migrateShopSelectionToCartIds();
                }
                if (this.selectedCartIds.length === 0) {
                    this.selectAllCartItems();
                }
            }
            this.syncSelectedShopIds();
        },

        migrateShopSelectionToCartIds() {
            const shopIds = this.selectedShopIds.map(normalizeId);
            const ids = [];
            this.products.forEach((shop) => {
                if (shopIds.includes(normalizeId(shop.shop_id))) {
                    shop.products.forEach((product) => {
                        if (product.cart_id != null) {
                            ids.push(normalizeId(product.cart_id));
                        }
                    });
                }
            });
            if (ids.length > 0) {
                this.selectedCartIds = ids;
            }
        },

        getCheckoutPayload(extra = {}) {
            const payload = { ...extra };
            if (this.getAllCartIds().length > 0) {
                payload.cart_ids = this.selectedCartIds.map(normalizeId);
                return payload;
            }
            if (this.selectedShopIds.length > 0) {
                payload.shop_ids = this.selectedShopIds.map(normalizeId);
            }
            return payload;
        },

        addToCart(data, product) {
            const masterStore = useMaster();
            if (data.product_id) {
                this.isLoadingCart = true;
                const content = {
                    component: AddToCartDialog,
                    props: {
                        product: product,
                    },
                };
                const authStore = useAuth();
                const previousCartIds = this.getAllCartIds();
                axios.post("/cart/store", data, {
                    headers: {
                        Authorization: authStore.token,
                    },
                }).then((response) => {
                    this.isLoadingCart = false;
                    if (!data.is_buy_now) {
                        this.total = response.data.data.total;
                        this.products = response.data.data.cart_items;
                        this.onCartItemsUpdated(previousCartIds);
                        this.fetchCheckoutProducts();

                        toast(content, {
                            type: "default",
                            hideProgressBar: true,
                            icon: false,
                            position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                            toastClassName: "vue-toastification-alert",
                            timeout: 3000,
                        });

                        if (response.data.data.info) {
                            toast.warning(response.data.data.info, {
                                position: authStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                            });
                        }
                    }
                }).catch((error) => {
                    this.isLoadingCart = false;
                    if (error.response.status == 401) {
                        toast.error("Please login first!", {
                            position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                        });
                        const authStore = useAuth();
                        authStore.logout();
                        authStore.showLoginModal();
                    } else {
                        toast.error(error.response.data.message, {
                            position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                        });
                    }
                    return error;
                });
            }
        },

        fetchCart() {
            const authStore = useAuth();
            if (authStore.token) {
                axios.get("/carts", {
                    headers: {
                        Authorization: authStore.token,
                    },
                }).then((response) => {
                    this.total = response.data.data.total;
                    this.products = response.data.data.cart_items;
                    this.onCartItemsUpdated();
                    this.fetchCheckoutProducts();

                    if (response.data.data.info) {
                        toast.warning(response.data.data.info, {
                            position: authStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                        });
                    }
                }).catch((error) => {
                    if (error.response.status === 401) {
                        authStore.token = null;
                        authStore.user = null;
                        authStore.addresses = [];
                        authStore.favoriteProducts = 0;
                    }
                });
            } else {
                this.total = 0;
                this.products = [];
                this.checkoutProducts = [];
                this.selectedShopIds = [];
                this.selectedCartIds = [];
                this.total_amount = 0;
                this.delivery_charge = 0;
                this.coupon_discount = 0;
                this.payable_amount = 0;
                this.coupon_code = "";
                this.address = null;
                authStore.user = null;
                authStore.addresses = [];
                authStore.token = null;
            }
        },

        decrementQuantity(product) {
            const authStore = useAuth();
            const masterStore = useMaster();
            if (product) {
                const content = {
                    component: RemoveCartPopupDialog,
                    props: {
                        product: product,
                    },
                };
                const removedCartId = product.cart_id
                    ? normalizeId(product.cart_id)
                    : null;
                axios.post("/cart/decrement",
                    {
                        product_id: product.id
                    },
                    {
                        headers: {
                            Authorization: authStore.token,
                        },
                    }
                ).then((response) => {
                    this.total = response.data.data.total;
                    this.products = response.data.data.cart_items;

                    if (removedCartId) {
                        this.selectedCartIds = this.selectedCartIds.filter(
                            (id) => normalizeId(id) !== removedCartId
                        );
                    }
                    this.pruneInvalidCartSelections();
                    this.fetchCheckoutProducts();

                    if (
                        response.data.message == "product removed from cart"
                    ) {
                        if (this.products.length === 0) {
                            this.selectedShopIds = [];
                            this.selectedCartIds = [];
                            this.checkoutProducts = [];
                            this.total_amount = 0;
                            this.delivery_charge = 0;
                            this.coupon_discount = 0;
                            this.payable_amount = 0;
                        }

                        toast(content, {
                            type: "default",
                            hideProgressBar: true,
                            icon: false,
                            position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                            toastClassName: "vue-toastification-alert",
                            timeout: 3000,
                        });

                        if (response.data.data.info) {
                            toast.warning(response.data.data.info, {
                                position: authStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                            });
                        }
                    }
                }).catch((error) => {
                    if (error.response.status == 401) {
                        authStore.token = null;
                        authStore.user = null;
                        authStore.addresses = [];
                        authStore.favoriteProducts = 0;
                    }
                });
            }
        },

        incrementQuantity(product) {
            const authStore = useAuth();
            const masterStore = useMaster();
            if (product) {
                axios.post("/cart/increment", {
                    product_id: product.id
                }, {
                    headers: {
                        Authorization: authStore.token,
                    },
                }).then((response) => {
                    this.total = response.data.data.total;
                    this.products = response.data.data.cart_items;
                    this.pruneInvalidCartSelections();
                    this.fetchCheckoutProducts();

                    if (response.data.data.info) {
                        toast.warning(response.data.data.info, {
                            position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                        });
                    }
                }).catch((error) => {
                    toast.error(error.response.data.message, {
                        position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                    });
                    if (error.response.status == 401) {
                        authStore.token = null;
                        authStore.user = null;
                        authStore.addresses = [];
                        authStore.favoriteProducts = 0;
                    }
                });
            }
        },

        removeFromBasket(product) {
            const authStore = useAuth();
            if (product) {
                const removedCartId = product.cart_id
                    ? normalizeId(product.cart_id)
                    : null;
                axios.post("/cart/delete",
                    { product_id: product.id },
                    {
                        headers: {
                            Authorization: authStore.token,
                        },
                    }
                ).then((response) => {
                    this.total = response.data.data.total;
                    this.products = response.data.data.cart_items;

                    if (removedCartId) {
                        this.selectedCartIds = this.selectedCartIds.filter(
                            (id) => normalizeId(id) !== removedCartId
                        );
                    }
                    this.pruneInvalidCartSelections();
                    this.fetchCheckoutProducts();

                    if (this.products.length === 0) {
                        this.selectedShopIds = [];
                        this.selectedCartIds = [];
                        this.checkoutProducts = [];
                        this.total_amount = 0;
                        this.delivery_charge = 0;
                        this.coupon_discount = 0;
                        this.payable_amount = 0;
                    }

                    if (response.data.data.info) {
                        toast.warning(response.data.data.info, {
                            position: authStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                        });
                    }
                }).catch((error) => {
                    if (error.response.status == 401) {
                        authStore.token = null;
                        authStore.user = null;
                        authStore.addresses = [];
                        authStore.favoriteProducts = 0;
                    }
                });
            }
        },

        toggleCartItem(cartId) {
            const id = normalizeId(cartId);
            const selected = this.selectedCartIds.map(normalizeId);

            if (selected.includes(id)) {
                this.selectedCartIds = this.selectedCartIds.filter(
                    (item) => normalizeId(item) !== id
                );
            } else {
                this.selectedCartIds.push(id);
            }

            this.syncSelectedShopIds();
            this.fetchCheckoutProducts();
        },

        selectCartItemsForCheckout(shopId) {
            const shop = normalizeId(shopId);
            const cartIdsInShop = [];

            this.products.forEach((item) => {
                if (normalizeId(item.shop_id) === shop) {
                    item.products.forEach((product) => {
                        if (product.cart_id != null) {
                            cartIdsInShop.push(normalizeId(product.cart_id));
                        }
                    });
                }
            });

            const selected = this.selectedCartIds.map(normalizeId);
            const allSelected =
                cartIdsInShop.length > 0 &&
                cartIdsInShop.every((id) => selected.includes(id));

            if (allSelected) {
                this.selectedCartIds = this.selectedCartIds.filter(
                    (id) => !cartIdsInShop.includes(normalizeId(id))
                );
            } else {
                cartIdsInShop.forEach((id) => {
                    if (!selected.includes(id)) {
                        this.selectedCartIds.push(id);
                    }
                });
            }

            this.syncSelectedShopIds();
            this.fetchCheckoutProducts();
        },

        fetchCheckoutProducts() {
            const authStore = useAuth();
            const masterStore = useMaster();
            if (authStore.token) {
                axios.post("/cart/checkout", this.getCheckoutPayload(), {
                    headers: {
                        Authorization: authStore.token,
                    },
                }).then((response) => {
                    this.checkoutProducts = response.data.data.checkout_items;
                    this.total_amount = response.data.data.checkout.total_amount;
                    this.delivery_charge = response.data.data.checkout.delivery_charge;
                    this.coupon_discount = response.data.data.checkout.coupon_discount;
                    this.payable_amount = response.data.data.checkout.payable_amount;
                    this.order_tax_amount = response.data.data.checkout.order_tax_amount;
                    this.all_vat_taxes = response.data.data.checkout.all_vat_taxes;
                }).catch((error) => {
                    if (error.response.status == 401) {
                        authStore.token = null;
                        authStore.user = null;
                        authStore.addresses = [];
                        authStore.favoriteProducts = 0;
                    }
                    toast.error(error.response.data.message, {
                        position: masterStore.langDirection === 'rtl' ? "bottom-right" : "bottom-left",
                    });
                });
            }
        },

        checkCartIsSelected(cartId) {
            return this.selectedCartIds
                .map(normalizeId)
                .includes(normalizeId(cartId));
        },

        checkShopIsSelected(shopId) {
            const shop = normalizeId(shopId);
            const cartIdsInShop = [];

            this.products.forEach((item) => {
                if (normalizeId(item.shop_id) === shop) {
                    item.products.forEach((product) => {
                        if (product.cart_id != null) {
                            cartIdsInShop.push(normalizeId(product.cart_id));
                        }
                    });
                }
            });

            return (
                cartIdsInShop.length > 0 &&
                cartIdsInShop.every((id) =>
                    this.selectedCartIds.map(normalizeId).includes(id)
                )
            );
        },
    },

    persist: true,
});
