/**
 * Convert numbers to words (English).
 *
 * Supports integers up to trillions. For decimals, it will spell the integer part
 * and then append "point" followed by each digit.
 */

/**
 * @param {number|string|null|undefined} input
 * @returns {string}
 */
export default function convertNumberToWords(input) {
    if (input === null || input === undefined || input === "") return "";

    const n = Number(String(input).replace(/[, ]+/g, ""));
    if (!Number.isFinite(n)) return "";

    if (n === 0) return "zero";
    if (n < 0) return `minus ${numberToWord(Math.abs(n))}`;

    const [intPart, fracPart] = String(n).split(".");

    const intWords = integerToWords(Number(intPart));

    if (!fracPart) return intWords;

    const fracWords = fracPart
        .split("")
        .map((d) => digitToWord(d))
        .join(" ");

    return `${intWords} point ${fracWords}`.trim();
}

const ONES = [
    "",
    "one",
    "two",
    "three",
    "four",
    "five",
    "six",
    "seven",
    "eight",
    "nine",
];
const TEENS = [
    "ten",
    "eleven",
    "twelve",
    "thirteen",
    "fourteen",
    "fifteen",
    "sixteen",
    "seventeen",
    "eighteen",
    "nineteen",
];
const TENS = [
    "",
    "",
    "twenty",
    "thirty",
    "forty",
    "fifty",
    "sixty",
    "seventy",
    "eighty",
    "ninety",
];

const SCALES = [
    { value: 1_000_000_000_000, name: "trillion" },
    { value: 1_000_000_000, name: "billion" },
    { value: 1_000_000, name: "million" },
    { value: 1_000, name: "thousand" },
];

/**
 * @param {number} n
 * @returns {string}
 */
function integerToWords(n) {
    if (n === 0) return "zero";

    let num = Math.floor(n);
    let words = [];

    for (const scale of SCALES) {
        if (num >= scale.value) {
            const count = Math.floor(num / scale.value);
            num = num % scale.value;
            words.push(`${chunkToWords(count)} ${scale.name}`.trim());
        }
    }

    if (num > 0) {
        words.push(chunkToWords(num));
    }

    return words.join(" ").replace(/\s+/g, " ").trim();
}

/**
 * @param {number} n (0..999)
 * @returns {string}
 */
function chunkToWords(n) {
    let num = n;
    let parts = [];

    if (num >= 100) {
        const hundreds = Math.floor(num / 100);
        parts.push(`${ONES[hundreds]} hundred`);
        num = num % 100;
    }

    if (num >= 20) {
        const tens = Math.floor(num / 10);
        const ones = num % 10;
        parts.push(TENS[tens] + (ones ? `-${ONES[ones]}` : ""));
        return parts.join(" ").trim();
    }

    if (num >= 10) {
        parts.push(TEENS[num - 10]);
        return parts.join(" ").trim();
    }

    if (num > 0) {
        parts.push(ONES[num]);
    }

    return parts.join(" ").trim();
}

/**
 * @param {string} d
 * @returns {string}
 */
function digitToWord(d) {
    switch (d) {
        case "0":
            return "zero";
        case "1":
            return "one";
        case "2":
            return "two";
        case "3":
            return "three";
        case "4":
            return "four";
        case "5":
            return "five";
        case "6":
            return "six";
        case "7":
            return "seven";
        case "8":
            return "eight";
        case "9":
            return "nine";
        default:
            return "";
    }
}
