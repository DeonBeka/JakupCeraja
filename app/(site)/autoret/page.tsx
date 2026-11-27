import type { Metadata } from "next";
import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { getAuthors } from "@/lib/queries";
import { excerpt } from "@/lib/format";

export const metadata: Metadata = { title: "Autorët" };

export default async function AuthorsPage() {
  const authors = await getAuthors();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Autorët tanë</span>
          <h1>Zëra që pasurojnë letërsinë</h1>
          <p>Njihuni me autorët dhe krijimtarinë që janë pjesë e botimeve tona.</p>
        </div>
      </section>
      <section className="section">
        <div className="container">
          <div className="authors-grid">
            {authors.map((a) => (
              <Link key={a.id} href={`/autoret/${a.id}`} className="author-card">
                <div className="author-photo">
                  {a.photo ? (
                    <img src={a.photo} alt={a.fullname} loading="lazy" />
                  ) : (
                    <span className="initials">{a.fullname.split(/\s+/).map((p) => p[0]).slice(0, 2).join("")}</span>
                  )}
                </div>
                <div className="author-body">
                  <small>{Number(a.count) === 1 ? "1 libër" : `${a.count} libra`}</small>
                  <h3>{a.fullname}</h3>
                  <p>{excerpt(a.biography, 120)}</p>
                  <span className="link-arrow">
                    Shiko profilin <ArrowRight />
                  </span>
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>
    </>
  );
}
