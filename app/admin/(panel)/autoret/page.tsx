import Link from "next/link";
import { Pencil, Plus, UserRound } from "lucide-react";
import { getAuthors } from "@/lib/queries";
import { deleteAuthor } from "../../actions";
import { ConfirmButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Autorët" };

export default async function AdminAuthors({ searchParams }: { searchParams: Promise<{ ok?: string }> }) {
  const sp = await searchParams;
  const list = await getAuthors();
  return (
    <>
      <Flash ok={sp.ok} />
      <section className="a-card">
        <div className="a-card-head">
          <h2>Autorët ({list.length})</h2>
          <Link href="/admin/autoret/i-ri" className="a-btn">
            <Plus /> Shto autor
          </Link>
        </div>
        {list.length ? (
          <div className="rows">
            {list.map((a) => (
              <div className="row" key={a.id}>
                <div className="row-thumb round">{a.photo ? <img src={a.photo} alt="" /> : <UserRound />}</div>
                <div className="row-main">
                  <strong>{a.fullname}</strong>
                  <span>{Number(a.count) === 1 ? "1 libër" : `${a.count} libra`}</span>
                </div>
                <div className="row-actions">
                  <Link href={`/admin/autoret/${a.id}`} className="a-icon-btn" aria-label="Ndrysho">
                    <Pencil />
                  </Link>
                  <ConfirmButton
                    action={deleteAuthor.bind(null, a.id)}
                    message={`Ta fshij autorin "${a.fullname}"? Librat e tij mbeten, por pa autor.`}
                    className="a-icon-btn danger"
                  />
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="a-empty">
            <UserRound />
            Ende nuk ka autorë.
          </div>
        )}
      </section>
    </>
  );
}
