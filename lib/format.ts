export function money(value: number | string | null | undefined) {
  const n = Number(value ?? 0);
  return n.toLocaleString("de-DE", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + " €";
}

export function dateAl(value: string | Date | null | undefined) {
  if (!value) return "";
  const d = typeof value === "string" ? new Date(value.length === 10 ? value + "T00:00:00" : value) : value;
  if (Number.isNaN(d.getTime())) return "";
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  return `${dd}.${mm}.${d.getFullYear()}`;
}

export function dateTimeAl(value: string | Date | null | undefined) {
  if (!value) return "";
  const d = typeof value === "string" ? new Date(value) : value;
  return `${dateAl(d)} ${String(d.getHours()).padStart(2, "0")}:${String(d.getMinutes()).padStart(2, "0")}`;
}

export function excerpt(text: string | null | undefined, max = 140) {
  const t = (text ?? "").replace(/\s+/g, " ").trim();
  return t.length > max ? t.slice(0, max).replace(/\s+\S*$/, "") + "…" : t;
}

export function paragraphs(text: string | null | undefined) {
  return (text ?? "")
    .split(/\n\s*\n|\r\n\s*\r\n/)
    .map((p) => p.trim())
    .filter(Boolean);
}

export const ORDER_STATUSES = {
  new: "E re",
  confirmed: "Konfirmuar",
  shipped: "Dërguar",
  completed: "Përfunduar",
  cancelled: "Anuluar",
} as const;

export type OrderStatus = keyof typeof ORDER_STATUSES;
