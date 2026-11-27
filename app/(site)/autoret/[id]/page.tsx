import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft } from "lucide-react";
import { BookCard } from "@/components/book-card";
import { getAuthor, getBooksByAuthor } from "@/lib/queries";
import { excerpt, paragraphs } from "@/lib/format";

type Props = { params: Promise<{ id: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const a = await getAuthor(Number((await params).id));
  return a ? { title: a.fullname, description: excerpt(a.biography, 160) } : { title: "Autori nuk u gjet" };
}

export default async function AuthorPage({ params }: Props) {
  const id = Number((await params).id);
  const author = Number.isFinite(id) ? await getAuthor(id) : undefined;
  if (!author) notFound();
  const books = await getBooksByAuthor(id);

  return (
    <>
      <section className="section">
        <div className="container">
          <Link href="/autoret" className="link-arrow">
            <ArrowLeft /> Të gjithë autorët
          </Link>
          <div className="profile" style={{ marginTop: 30 }}>
            <div className="author-photo">
              {author.photo ? (
                <img src={author.photo} alt={author.fullname} />
              ) : (
                <span className="initials">{author.fullname.split(/\s+/).map((p) => p[0]).slice(0, 2).join("")}</span>
              )}
            </div>
            <div>
              <span className="tag">Autor</span>
              <h1 style={{ fontSize: "clamp(2rem, 4.4vw, 3rem)", margin: "12px 0 24px" }}>{author.fullname}</h1>
              <div className="prose">
                {paragraphs(author.biography).map((p, i) => (
                  <p key={i}>{p}</p>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>
      {books.length > 0 && (
        <section className="section section-alt">
          <div className="container">
            <div className="section-head-row">
              <div>
                <span className="tag">Botimet</span>
                <h2>Librat e {author.fullname}</h2>
              </div>
            </div>
            <div className="books-grid">
              {books.map((b) => (
                <BookCard key={b.id} book={b} />
              ))}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
