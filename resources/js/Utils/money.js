import { usePage } from "@inertiajs/inertia-vue3";

/**
 * Returns the app currency symbol from Inertia shared props.
 * Falls back to "$" if not available.
 */
export function useCurrencySymbol() {
    const page = usePage();
    return page?.props?.value?.app_currency_symbol ?? "$";
}

/**
 * Format a number as money using the app currency symbol.
 *
 * Note: This keeps the existing number formatting behavior (en-US, 2 decimals)
 * and only makes the currency symbol dynamic.
 */
export function useMoneyFormatter() {
    const currencySymbol = useCurrencySymbol();

    const formatNumber = (value) => {
        if (value === null || value === undefined || value === "")
            return "0.00";
        const n = Number(value);
        if (Number.isNaN(n)) return "0.00";

        return n.toLocaleString("en-US", {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    const money = (value) => `${currencySymbol}${formatNumber(value)}`;

    return { money, formatNumber, currencySymbol };
}
