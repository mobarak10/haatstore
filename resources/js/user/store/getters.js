export default {
    getterSingleActiveProduct: state => barcode => {
        return state.activeProducts.find(
            product => product.barcode === barcode
        );
    },
    getterSingleActiveProductByProductCode: state => productCode => {
        return state.activeProducts.find(
            product => product.code === productCode
        );
    },
    getterSingleCartItem: state => productId => {
        return state.cartProducts[productId];
    },

    getterSortedCartProducts: state => {
        const data = state.cartProducts
        const _purchase_items_value = Object.keys(data).map((key) => data[key])

        _purchase_items_value
            .sort(function (a, b) {
                return b.attributes.meta.created_at - a.attributes.meta.created_at
            })

        return _purchase_items_value

    },
    getterFilterWiseProducts: state => payload => {
        if (payload.categoryId !== null) {
            return state.activeProducts.filter(
                product => product["category_id"] == payload["categoryId"]
            );
        } else if (
            payload.productName != null &&
            payload.productName.length > 0
        ) {
            return state.activeProducts.filter(product =>
                product.name
                    .toLowerCase()
                    .includes(payload.productName.toLowerCase())
            );
        } else {
            return state.activeProducts;
        }
    }
};
