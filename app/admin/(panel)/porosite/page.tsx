import Link from "next/link";
import { and, count, desc, eq, ilike, inArray, ne, or, sum } from "drizzle-orm";
import { BookOpen, ChevronDown, Euro, Inbox, Mail, MapPin, MessageSquare, Phone, Search, ShoppingCart, Truck } from "lucide-react";
import { getDb } from "@/lib/db";
import { orderItems, orders } from "@/lib/schema";
import { dateTimeAl, money, ORDER_STATUSES, type OrderStatus } from "@/lib/format";
import { deleteOrder, setOrderStatus } from "../../actions";
import { ConfirmButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Porositë" };

export default async function AdminOrders({ searchParams }: { searchParams: Promise<Record<string, string | undefined>> }) {
  const sp = await searchParams;
  const db = await getDb();
  const status = sp.statusi && sp.statusi in ORDER_STATUSES ? sp.statusi : "";
  const q = (sp.q ?? "").trim();
  const open = Number(sp.hap) || 0;

  const conds = [];
  if (status) conds.push(eq(orders.status, status));
  if (q) {
    const like = `%${q}%`;
    conds.push(or(ilike(orders.fullname, like), ilike(orders.phone, like), ilike(orders.code, like), ilike(orders.email, like), ilike(orders.city, like)));
  }

  const [list, counts, [rev]] = await Promise.all([
    db.select().from(orders).where(conds.length ? and(...conds) : undefined).orderBy(desc(orders.createdAt)).limit(200),
    db.select({ status: orders.status, c: count() }).from(orders).groupBy(orders.status),
    db.select({ t: sum(orders.total) }).from(orders).where(ne(orders.status, "cancelled")),
  ]);
  const items = list.length ? await db.select().from(orderItems).where(inArray(orderItems.orderId, list.map((o) => o.id))) : [];
  const n = (s: string) => Number(counts.find((c) => c.status === s)?.c ?? 0);
  const all = counts.reduce((s, c) => s + Number(c.c), 0);
  const tabHref = (s: string) => {
    const u = new URLSearchParams();
    if (s) u.set("statusi", s);
    if (q) u.set("q", q);
    return `/admin/porosite${u.toString() ? "?" + u : ""}`;
  };

  return (
    <>
      <Flash ok={sp.ok} />
      <div className="kpis">
        <div className="kpi">
          <span>Të gjitha</span>
          <strong>{all}</strong>
          <ShoppingCart />
        </div>
        <div className="kpi warn">
          <span>Të reja</span>
          <strong>{n("new")}</strong>
          <Inbox />
        </div>
        <div className="kpi">
          <span>Në proces</span>
          <strong>{n("confirmed") + n("shipped")}</strong>
          <Truck />
        </div>
        <div className="kpi">
          <span>Të ardhura</span>
          <strong>{money(rev.t ?? 0)}</strong>
          <Euro />
        </div>
      </div>

      <section className="a-card">
        <form className="toolbar" action="/admin/porosite">
          {status && <input type="hidden" name="statusi" value={status} />}
          <label className="a-search">
            <Search />
            <input type="search" name="q" defaultValue={q} placeholder="Kërko emër, telefon, kod…" />
          </label>
          <div className="a-tabs">
            <Link href={tabHref("")} className={!status ? "active" : ""}>
              Të gjitha <em>{all}</em>
            </Link>
            {Object.entries(ORDER_STATUSES).map(([k, label]) => (
              <Link key={k} href={tabHref(k)} className={status === k ? "active" : ""}>
                {label} <em>{n(k)}</em>
              </Link>
            ))}
          </div>
        </form>

        {list.length ? (
          list.map((o) => {
            const lines = items.filter((i) => i.orderId === o.id);
            const qty = lines.reduce((s, l) => s + l.quantity, 0);
            return (
              <details key={o.id} className={`order${o.status === "new" ? " is-new" : ""}`} open={o.id === open}>
                <summary>
                  <span className="order-code">{o.code}</span>
                  <div className="row-main">
                    <strong>{o.fullname}</strong>
                    <span>
                      {o.city} · {dateTimeAl(o.createdAt)}
                    </span>
                  </div>
                  <span className="row-meta hide-sm">
                    {qty} {qty === 1 ? "libër" : "libra"}
                  </span>
                  <span className={`status ${o.status}`}>{ORDER_STATUSES[o.status as OrderStatus] ?? o.status}</span>
                  <span className="row-price">{money(o.total)}</span>
                  <ChevronDown className="chev" />
                </summary>
                <div className="order-body">
                  <div>
                    {lines.map((l) => (
                      <div className="order-line" key={l.id}>
                        <div className="row-thumb">{l.cover ? <img src={l.cover} alt="" /> : <BookOpen />}</div>
                        <div className="row-main">
                          <strong>{l.title}</strong>
                          <span>
                            {l.quantity} × {money(l.unitPrice)}
                          </span>
                        </div>
                        <strong>{money(Number(l.unitPrice) * l.quantity)}</strong>
                      </div>
                    ))}
                    <div className="order-sums" style={{ paddingTop: 10 }}>
                      <div>
                        <span>Librat</span>
                        <span>{money(o.subtotal)}</span>
                      </div>
                      <div>
                        <span>Dërgesa</span>
                        <span>{Number(o.shipping) > 0 ? money(o.shipping) : "Falas"}</span>
                      </div>
                      <div className="tot">
                        <span>Totali (paguhet në dorëzim)</span>
                        <span>{money(o.total)}</span>
                      </div>
                    </div>
                  </div>
                  <div style={{ display: "flex", flexDirection: "column", gap: 14 }}>
                    <div className="contact-box">
                      <span style={{ fontSize: "0.75rem", fontWeight: 700, letterSpacing: ".1em", textTransform: "uppercase", color: "var(--mu)" }}>
                        Klienti · {o.code}
                      </span>
                      <a href={`tel:${o.phone.replace(/[^\d+]/g, "")}`}>
                        <Phone /> {o.phone}
                      </a>
                      <a href={`mailto:${o.email}?subject=${encodeURIComponent("Porosia " + o.code)}`}>
                        <Mail /> {o.email}
                      </a>
                      <span style={{ display: "flex", gap: 10 }}>
                        <MapPin style={{ width: 15, height: 15, color: "var(--green)", flexShrink: 0, marginTop: 3 }} />
                        {o.address}, {o.city}, {o.country}
                      </span>
                      {o.note && (
                        <span style={{ display: "flex", gap: 10, background: "#fff", padding: "8px 10px", borderRadius: 8 }}>
                          <MessageSquare style={{ width: 15, height: 15, color: "var(--green)", flexShrink: 0, marginTop: 3 }} />
                          {o.note}
                        </span>
                      )}
                    </div>
                    <form action={setOrderStatus.bind(null, o.id)} style={{ display: "flex", flexDirection: "column", gap: 6 }}>
                      <label className="a-field" htmlFor={`st-${o.id}`}>
                        Statusi i porosisë
                      </label>
                      <div style={{ display: "flex", gap: 8 }}>
                        <select
                          id={`st-${o.id}`}
                          name="status"
                          defaultValue={o.status}
                          style={{ flex: 1, padding: "10px 12px", border: "1px solid #d9ded9", borderRadius: 10, background: "#fff" }}
                        >
                          {Object.entries(ORDER_STATUSES).map(([k, label]) => (
                            <option key={k} value={k}>
                              {label}
                            </option>
                          ))}
                        </select>
                        <button type="submit" className="a-btn">
                          Ruaj
                        </button>
                      </div>
                    </form>
                    <ConfirmButton action={deleteOrder.bind(null, o.id)} message={`Ta fshij porosinë ${o.code}?`} label="Fshi porosinë" className="a-btn danger" />
                  </div>
                </div>
              </details>
            );
          })
        ) : (
          <div className="a-empty">
            <Inbox />
            {status || q ? "Asnjë porosi nuk përputhet me kërkimin." : "Ende nuk ka porosi. Kur dikush blen libra, porositë shfaqen këtu."}
          </div>
        )}
      </section>
    </>
  );
}
