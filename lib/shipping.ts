// Shipping rules:
// - Kosovë: free
// - Shqipëri and Maqedoni e Veriut: the price set in Admin → Cilësimet (default 2 €)
// - any other country: the customer contacts us and we calculate the price

export const OTHER_COUNTRY = "Shtet tjetër";
export const SHIPPING_COUNTRIES = ["Kosovë", "Shqipëri", "Maqedoni e Veriut", OTHER_COUNTRY] as const;

/** Shipping cost for a country, or null when it must be agreed with us personally. */
export function shippingFor(country: string, regionalPrice: number): number | null {
  if (country === "Kosovë") return 0;
  if (country === "Shqipëri" || country === "Maqedoni e Veriut") return regionalPrice;
  return null;
}
