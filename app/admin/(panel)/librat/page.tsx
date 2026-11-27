import Link from "next/link";
import { BookOpen, Pencil, Plus, Search } from "lucide-react";
import { getBooks } from "@/lib/queries";
import { money } from "@/lib/format";
import { deleteBook } from "../../actions";
import { ConfirmButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Librat" };

export default async function AdminBooks({ searchParams }: { searchParams: Promise<Record<string, string | undefined>> }) {
  const sp = await searchParams;
  const q = (sp.q ?? "").trim();
  let list = await getBooks({ q });
  if (sp["pa-cmim"]) list = list.filter((b) => Number(b.price) <= 0);

  return (
    <>
      <Flash ok={sp.ok} />
      <section className="a-card">
        <div className="a-card-head">
          <h2>Të gjithë librat ({list.length})</h2>
          <Link href="/admin/librat/i-ri" className="a-btn">
            <Plus /> Shto libër
          </Link>
        </div>
        <form className="toolbar">
          <label className="a-search">
            <Search />
            <input type="search" name="q" defaultValue={q} placeholder="Kërko titull ose autor…" />
          </label>
          <div className="a-tabs">
            <Link href="/admin/librat" className={!sp["pa-cmim"] ? "active" : ""}>
              Të gjithë
            </Link>
            <Link href="/admin/librat?pa-cmim=1" className={sp["pa-cmim"] ? "active" : ""}>
              Pa çmim
            </Link>
          </div>
        </form>
        {list.length ? (
          <div className="rows">
            {list.map((b) => (
              <div className="row" key={b.id}>
                <div className="row-thumb">{b.cover ? <img src={b.cover} alt="" /> : <BookOpen />}</div>
                <div className="row-main">
                  <strong>{b.title}</strong>
                  <span>
                    {b.authorName ?? "Pa autor"} · {b.categoryName ?? "Pa kategori"}
                  </span>
                </div>
                <div className="row-meta hide-sm" style={{ display: "flex", gap: 6 }}>
                  {b.featured && <span className="tag-s star">I veçuar</span>}
                  {!b.inStock && <span className="tag-s wine">Jashtë stokut</span>}
                </div>
                <span className="row-price">{Number(b.price) > 0 ? money(b.price) : <span className="tag-s wine">Pa çmim</span>}</span>
                <div className="row-actions">
                  <Link href={`/admin/librat/${b.id}`} className="a-icon-btn" aria-label="Ndrysho">
                    <Pencil />
                  </Link>
                  <ConfirmButton
                    action={deleteBook.bind(null, b.id)}
                    message={`Ta fshij librin "${b.title}"?`}
                    className="a-icon-btn danger"
                  />
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="a-empty">
            <BookOpen />
            Nuk u gjet asnjë libër.
          </div>
        )}
      </section>
    </>
  );
}
