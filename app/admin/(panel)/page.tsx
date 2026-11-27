import Link from "next/link";
import { count, desc, eq, ne, sum } from "drizzle-orm";
import { BookOpen, Euro, Mail, Newspaper, PenLine, Plus, ShoppingCart, ArrowRight, Inbox } from "lucide-react";
import { getDb } from "@/lib/db";
import { authors, books, messages, orders } from "@/lib/schema";
import { getSession } from "@/lib/auth";
import { dateTimeAl, money, ORDER_STATUSES, type OrderStatus } from "@/lib/format";

export const metadata = { title: "Paneli" };

export default async function Dashboard() {
  const db = await getDb();
  const s = await getSession();
  const [[b], [a], [o], [newO], [rev], recent, recentMsgs, noPrice] = await Promise.all([
    db.select({ c: count() }).from(books),
    db.select({ c: count() }).from(authors),
    db.select({ c: count() }).from(orders),
    db.select({ c: count() }).from(orders).where(eq(orders.status, "new")),
    db.select({ t: sum(orders.total) }).from(orders).where(ne(orders.status, "cancelled")),
    db.select().from(orders).orderBy(desc(orders.createdAt)).limit(6),
    db.select().from(messages).orderBy(desc(messages.createdAt)).limit(5),
    db.select({ c: count() }).from(books).where(eq(books.price, "0")),
  ]);

  return (
    <>
      <div className="welcome">
        <div>
          <h2>Mirë se vini, {s?.name}!</h2>
          <p>Këtu menaxhoni librat, porositë dhe gjithçka në faqe.</p>
        </div>
        <div className="quick">
          <Link href="/admin/librat/i-ri">
            <Plus /> Shto libër
          </Link>
          <Link href="/admin/lajmet/i-ri">
            <Newspaper /> Shto lajm
          </Link>
          <Link href="/admin/porosite">
            <ShoppingCart /> Porositë
          </Link>
        </div>
      </div>

      {Number(noPrice[0].c) > 0 && (
        <div className="a-alert err">
          <Euro />
          <span>
            {noPrice[0].c} libra nuk kanë çmim dhe nuk mund të blihen.{" "}
            <Link href="/admin/librat?pa-cmim=1" style={{ textDecoration: "underline" }}>
              Vendosni çmimet
            </Link>
          </span>
        </div>
      )}

      <div className="kpis">
        <Link href="/admin/porosite?statusi=new" className="kpi warn">
          <span>Porosi të reja</span>
          <strong>{newO.c}</strong>
          <ShoppingCart />
        </Link>
        <Link href="/admin/porosite" className="kpi">
          <span>Të ardhura</span>
          <strong>{money(rev.t ?? 0)}</strong>
          <Euro />
        </Link>
        <Link href="/admin/librat" className="kpi">
          <span>Libra</span>
          <strong>{b.c}</strong>
          <BookOpen />
        </Link>
        <Link href="/admin/autoret" className="kpi wine">
          <span>Autorë</span>
          <strong>{a.c}</strong>
          <PenLine />
        </Link>
      </div>

      <div className="grid-2">
        <section className="a-card">
          <div className="a-card-head">
            <h2>Porositë e fundit</h2>
            <Link href="/admin/porosite" className="a-btn light">
              Të gjitha ({o.c}) <ArrowRight />
            </Link>
          </div>
          {recent.length ? (
            <div className="rows">
              {recent.map((r) => (
                <Link key={r.id} href={`/admin/porosite?hap=${r.id}`} className="row">
                  <div className="row-main">
                    <strong>{r.fullname}</strong>
                    <span>
                      {r.code} · {r.city} · {dateTimeAl(r.createdAt)}
                    </span>
                  </div>
                  <span className={`status ${r.status}`}>{ORDER_STATUSES[r.status as OrderStatus] ?? r.status}</span>
                  <span className="row-price">{money(r.total)}</span>
                </Link>
              ))}
            </div>
          ) : (
            <div className="a-empty">
              <Inbox />
              Ende nuk ka porosi. Kur dikush blen libra, shfaqen këtu.
            </div>
          )}
        </section>

        <section className="a-card">
          <div className="a-card-head">
            <h2>Mesazhet e fundit</h2>
            <Link href="/admin/mesazhet" className="a-btn light">
              Të gjitha <ArrowRight />
            </Link>
          </div>
          {recentMsgs.length ? (
            <div className="rows">
              {recentMsgs.map((m) => (
                <Link key={m.id} href={`/admin/mesazhet?hap=${m.id}`} className="row">
                  {!m.isRead && <span className="unread" />}
                  <div className="row-main">
                    <strong>{m.fullname}</strong>
                    <span>{m.subject || m.message.slice(0, 60)}</span>
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="a-empty">
              <Mail />
              Asnjë mesazh ende.
            </div>
          )}
        </section>
      </div>
    </>
  );
}
