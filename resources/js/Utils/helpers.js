/**
 * Shared formatting helpers.
 *
 * NOTE: This file was restored because `resources/js/app.js` imports:
 *   import { formatCurrency, formatNumber } from "./Utils/helpers";
 */

/**
 * Format a number as currency.
 *
 * @param {number|string|null|undefined} value
 * @param {object} [options]
 * @param {string} [options.currency] ISO 4217 currency code (default: "BDT")
 * @param {string} [options.locale] BCP 47 locale (default: "en-US")
 * @param {number} [options.minimumFractionDigits] (default: 2)
 * @param {number} [options.maximumFractionDigits] (default: 2)
 * @returns {string}
 */
export function formatCurrency(
    value,
    {
        currency = "BDT",
        locale = "en-US",
        minimumFractionDigits = 2,
        maximumFractionDigits = 2,
    } = {},
) {
    const number = toNumber(value);

    if (number === null) return "0";

    try {
        return new Intl.NumberFormat(locale, {
            style: "currency",
            currency,
            minimumFractionDigits,
            maximumFractionDigits,
        }).format(number);
    } catch {
        // Fallback if Intl fails or currency code is invalid
        return `${currency} ${formatNumber(number, {
            locale,
            minimumFractionDigits,
            maximumFractionDigits,
        })}`;
    }
}

/**
 * Format a number with grouping and fraction digits.
 *
 * @param {number|string|null|undefined} value
 * @param {object} [options]
 * @param {string} [options.locale] (default: "en-US")
 * @param {number} [options.minimumFractionDigits] (default: 0)
 * @param {number} [options.maximumFractionDigits] (default: 2)
 * @returns {string}
 */
export function formatNumber(
    value,
    {
        locale = "en-US",
        minimumFractionDigits = 0,
        maximumFractionDigits = 2,
    } = {},
) {
    const number = toNumber(value);

    if (number === null) return "0";

    try {
        return new Intl.NumberFormat(locale, {
            minimumFractionDigits,
            maximumFractionDigits,
        }).format(number);
    } catch {
        // Very old environments fallback
        return String(number);
    }
}

/**
 * @param {any} value
 * @returns {number|null}
 */
function toNumber(value) {
    if (value === null || value === undefined || value === "") return null;

    if (typeof value === "number") {
        return Number.isFinite(value) ? value : null;
    }

    // Remove common formatting characters (commas, spaces)
    const cleaned = String(value).replace(/[, ]+/g, "");
    const parsed = Number(cleaned);

    return Number.isFinite(parsed) ? parsed : null;
}
