import Link from "next/link";
import { BookOpen } from "lucide-react";
import type { BookRow } from "@/lib/queries";
import { money } from "@/lib/format";
import { AddToCart } from "./cart";

export function BookCover({ book }: { book: Pick<BookRow, "id" | "title" | "cover" | "categoryName"> }) {
  return (
    <>
      {book.categoryName && <span className="pill">{book.categoryName}</span>}
      {book.cover ? (
        <img src={book.cover} alt={book.title} loading="lazy" />
      ) : (
        <span className="book-placeholder">
          <span>
            <BookOpen />
            <span>{book.title}</span>
          </span>
        </span>
      )}
    </>
  );
}

export function BookCard({ book }: { book: BookRow }) {
  const price = Number(book.price);
  return (
    <article className="book-card">
      <Link href={`/librat/${book.id}`} className="book-cover" aria-label={book.title}>
        <BookCover book={book} />
      </Link>
      <div className="book-body">
        <h3>
          <Link href={`/librat/${book.id}`}>{book.title}</Link>
        </h3>
        {book.authorName && <span className="book-author">{book.authorName}</span>}
        <div className="book-foot">
          {price > 0 ? <span className="book-price">{money(price)}</span> : <span className="book-price muted">Çmimi së shpejti</span>}
          <AddToCart
            book={{ id: book.id, title: book.title, price, cover: book.cover, author: book.authorName, inStock: book.inStock }}
          />
        </div>
      </div>
    </article>
  );
}
