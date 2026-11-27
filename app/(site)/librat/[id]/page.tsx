import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ArrowLeft, ArrowRight, PenLine } from "lucide-react";
import { BookCard, BookCover } from "@/components/book-card";
import { AddToCart } from "@/components/cart";
import { getAuthor, getBook, getRelatedBooks } from "@/lib/queries";
import { dateAl, excerpt, money, paragraphs } from "@/lib/format";

type Props = { params: Promise<{ id: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const book = await getBook(Number((await params).id));
  return book ? { title: book.title, description: excerpt(book.description, 160) } : { title: "Libri nuk u gjet" };
}

export default async function BookPage({ params }: Props) {
  const id = Number((await params).id);
  const book = Number.isFinite(id) ? await getBook(id) : undefined;
  if (!book) notFound();

  const [author, related] = await Promise.all([book.authorId ? getAuthor(book.authorId) : undefined, getRelatedBooks(book)]);
  const price = Number(book.price);

  return (
    <>
      <section className="section">
        <div className="container">
          <Link href="/librat" className="link-arrow" style={{ marginBottom: 30 }}>
            <ArrowLeft /> Të gjithë librat
          </Link>
          <div className="detail" style={{ marginTop: 24 }}>
            <div className="detail-cover">
              <div className="book-cover">
                <BookCover book={{ ...book, categoryName: null }} />
              </div>
            </div>
            <div>
              {book.categoryName && <span className="pill">{book.categoryName}</span>}
              <h1>{book.title}</h1>
              {book.authorName && (
                <Link href={`/autoret/${book.authorId}`} className="detail-author">
                  <PenLine size={18} /> {book.authorName}
                </Link>
              )}

              <div className="detail-price">
                {price > 0 ? (
                  <>
                    <strong>{money(price)}</strong>
                    <span>Pagesa në dorëzim · Dërgesë në Kosovë dhe rajon</span>
                  </>
                ) : (
                  <span>Çmimi do të publikohet së shpejti. Na kontaktoni për porosi.</span>
                )}
              </div>

              <div className="detail-actions">
                <AddToCart
                  large
                  book={{ id: book.id, title: book.title, price, cover: book.cover, author: book.authorName, inStock: book.inStock }}
                />
                {price <= 0 && (
                  <Link href="/kontakti" className="btn btn-outline">
                    Na kontaktoni
                  </Link>
                )}
              </div>

              <div className="detail-meta">
                {book.authorName && (
                  <div>
                    <small>Autori</small>
                    <strong>{book.authorName}</strong>
                  </div>
                )}
                {book.categoryName && (
                  <div>
                    <small>Kategoria</small>
                    <strong>{book.categoryName}</strong>
                  </div>
                )}
                {book.publishDate && (
                  <div>
                    <small>Botuar</small>
                    <strong>{dateAl(book.publishDate)}</strong>
                  </div>
                )}
              </div>

              {book.description && (
                <div className="prose">
                  <h2>Për librin</h2>
                  {paragraphs(book.description).map((p, i) => (
                    <p key={i}>{p}</p>
                  ))}
                </div>
              )}

              {author && (
                <Link href={`/autoret/${author.id}`} className="author-box">
                  <div className="author-photo">
                    {author.photo ? <img src={author.photo} alt={author.fullname} /> : null}
                  </div>
                  <div>
                    <h3>{author.fullname}</h3>
                    <p>{excerpt(author.biography, 160)}</p>
                  </div>
                </Link>
              )}
            </div>
          </div>
        </div>
      </section>

      {related.length > 0 && (
        <section className="section section-alt">
          <div className="container">
            <div className="section-head-row">
              <div>
                <span className="tag">Mund t&apos;ju pëlqejnë</span>
                <h2>Libra të ngjashëm</h2>
              </div>
              <Link href={`/librat?kategoria=${book.categoryId}`} className="link-arrow">
                Më shumë nga {book.categoryName} <ArrowRight />
              </Link>
            </div>
            <div className="books-grid">
              {related.map((b) => (
                <BookCard key={b.id} book={b} />
              ))}
            </div>
          </div>
        </section>
      )}
    </>
  );
}
