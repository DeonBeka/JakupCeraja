import { desc, eq } from "drizzle-orm";
import { ChevronDown, Mail, Phone, Reply } from "lucide-react";
import { getDb } from "@/lib/db";
import { messages } from "@/lib/schema";
import { dateTimeAl, paragraphs } from "@/lib/format";
import { deleteMessage } from "../../actions";
import { ConfirmButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Mesazhet" };

export default async function AdminMessages({ searchParams }: { searchParams: Promise<{ ok?: string; hap?: string }> }) {
  const sp = await searchParams;
  const db = await getDb();
  const list = await db.select().from(messages).orderBy(desc(messages.createdAt));
  const open = Number(sp.hap);
  if (open) {
    const m = list.find((x) => x.id === open);
    if (m && !m.isRead) await db.update(messages).set({ isRead: true }).where(eq(messages.id, open));
  }

  return (
    <>
      <Flash ok={sp.ok} />
      <section className="a-card">
        <div className="a-card-head">
          <h2>Mesazhet nga faqja ({list.length})</h2>
        </div>
        {list.length ? (
          <div>
            {list.map((m) => (
              <details key={m.id} className={`order${!m.isRead && m.id !== open ? " is-new" : ""}`} open={m.id === open}>
                <summary>
                  {!m.isRead && m.id !== open && <span className="unread" />}
                  <div className="row-main">
                    <strong>{m.fullname}</strong>
                    <span>
                      {m.subject || m.message.slice(0, 70)} · {dateTimeAl(m.createdAt)}
                    </span>
                  </div>
                  <ChevronDown className="chev" />
                </summary>
                <div className="order-body">
                  <div className="prose" style={{ fontSize: "0.95rem" }}>
                    {m.subject && <h3 style={{ marginBottom: 10 }}>{m.subject}</h3>}
                    {paragraphs(m.message).map((p, i) => (
                      <p key={i}>{p}</p>
                    ))}
                  </div>
                  <div style={{ display: "flex", flexDirection: "column", gap: 12 }}>
                    <div className="contact-box">
                      <a href={`mailto:${m.email}?subject=${encodeURIComponent("Re: " + (m.subject || "Mesazhi juaj"))}`}>
                        <Mail /> {m.email}
                      </a>
                      {m.phone && (
                        <a href={`tel:${m.phone.replace(/[^\d+]/g, "")}`}>
                          <Phone /> {m.phone}
                        </a>
                      )}
                    </div>
                    <div style={{ display: "flex", gap: 8, flexWrap: "wrap" }}>
                      <a className="a-btn" href={`mailto:${m.email}?subject=${encodeURIComponent("Re: " + (m.subject || "Mesazhi juaj"))}`}>
                        <Reply /> Përgjigju me email
                      </a>
                      <ConfirmButton action={deleteMessage.bind(null, m.id)} message="Ta fshij këtë mesazh?" label="Fshi" />
                    </div>
                  </div>
                </div>
              </details>
            ))}
          </div>
        ) : (
          <div className="a-empty">
            <Mail />
            Asnjë mesazh ende. Mesazhet nga faqja &quot;Kontakti&quot; shfaqen këtu.
          </div>
        )}
      </section>
    </>
  );
}
