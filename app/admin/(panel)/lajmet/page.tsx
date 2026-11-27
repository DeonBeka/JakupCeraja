import Link from "next/link";
import { Newspaper, Pencil, Plus } from "lucide-react";
import { getNews } from "@/lib/queries";
import { dateAl } from "@/lib/format";
import { deleteNews } from "../../actions";
import { ConfirmButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Lajmet" };

export default async function AdminNews({ searchParams }: { searchParams: Promise<{ ok?: string }> }) {
  const sp = await searchParams;
  const list = await getNews();
  return (
    <>
      <Flash ok={sp.ok} />
      <section className="a-card">
        <div className="a-card-head">
          <h2>Lajmet ({list.length})</h2>
          <Link href="/admin/lajmet/i-ri" className="a-btn">
            <Plus /> Shto lajm
          </Link>
        </div>
        {list.length ? (
          <div className="rows">
            {list.map((n) => (
              <div className="row" key={n.id}>
                <div className="row-thumb square">{n.image ? <img src={n.image} alt="" /> : <Newspaper />}</div>
                <div className="row-main">
                  <strong>{n.title}</strong>
                  <span>
                    {dateAl(n.publishedAt ?? n.createdAt)}
                    {n.category ? ` · ${n.category}` : ""}
                  </span>
                </div>
                <div className="row-actions">
                  <Link href={`/admin/lajmet/${n.id}`} className="a-icon-btn" aria-label="Ndrysho">
                    <Pencil />
                  </Link>
                  <ConfirmButton action={deleteNews.bind(null, n.id)} message={`Ta fshij lajmin "${n.title}"?`} className="a-icon-btn danger" />
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="a-empty">
            <Newspaper />
            Ende nuk ka lajme.
          </div>
        )}
      </section>
    </>
  );
}
