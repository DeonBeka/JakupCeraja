import "server-only";
import { and, asc, count, desc, eq, ilike, or, sql } from "drizzle-orm";
import { getDb, type Db } from "./db";
import { authors, books, categories, gallery, news, settings } from "./schema";

const bookFields = {
  id: books.id,
  title: books.title,
  price: books.price,
  cover: books.cover,
  description: books.description,
  publishDate: books.publishDate,
  featured: books.featured,
  inStock: books.inStock,
  authorId: books.authorId,
  categoryId: books.categoryId,
  authorName: authors.fullname,
  categoryName: categories.name,
};

export type BookRow = {
  id: number;
  title: string;
  price: string;
  cover: string | null;
  description: string | null;
  publishDate: string | null;
  featured: boolean;
  inStock: boolean;
  authorId: number | null;
  categoryId: number | null;
  authorName: string | null;
  categoryName: string | null;
};

function bookQuery(db: Db) {
  return db
    .select(bookFields)
    .from(books)
    .leftJoin(authors, eq(authors.id, books.authorId))
    .leftJoin(categories, eq(categories.id, books.categoryId));
}

export async function getFeaturedBooks(limit = 8): Promise<BookRow[]> {
  return bookQuery(await getDb()).where(eq(books.featured, true)).orderBy(desc(books.id)).limit(limit);
}

export async function getLatestBooks(limit = 4): Promise<BookRow[]> {
  return bookQuery(await getDb()).orderBy(sql`${books.publishDate} DESC NULLS LAST`, desc(books.id)).limit(limit);
}

export async function getBooks(opts: { q?: string; category?: number; sort?: string } = {}): Promise<BookRow[]> {
  const conds = [];
  if (opts.q) {
    const like = `%${opts.q}%`;
    conds.push(or(ilike(books.title, like), ilike(authors.fullname, like), ilike(books.description, like)));
  }
  if (opts.category) conds.push(eq(books.categoryId, opts.category));
  const order =
    opts.sort === "price-asc"
      ? [asc(books.price)]
      : opts.sort === "price-desc"
        ? [desc(books.price)]
        : opts.sort === "title"
          ? [asc(books.title)]
          : [sql`${books.publishDate} DESC NULLS LAST`, desc(books.id)];
  return bookQuery(await getDb()).where(conds.length ? and(...conds) : undefined).orderBy(...order);
}

export async function getBook(id: number): Promise<BookRow | undefined> {
  const rows = await bookQuery(await getDb()).where(eq(books.id, id)).limit(1);
  return rows[0];
}

export async function getBooksByAuthor(authorId: number): Promise<BookRow[]> {
  return bookQuery(await getDb()).where(eq(books.authorId, authorId)).orderBy(desc(books.id));
}

export async function getRelatedBooks(book: BookRow, limit = 4): Promise<BookRow[]> {
  if (!book.categoryId) return [];
  const rows = await bookQuery(await getDb())
    .where(and(eq(books.categoryId, book.categoryId), sql`${books.id} <> ${book.id}`))
    .orderBy(desc(books.id))
    .limit(limit);
  return rows;
}

export async function getCategoriesWithCount() {
  const db = await getDb();
  return db
    .select({ id: categories.id, name: categories.name, icon: categories.icon, count: count(books.id) })
    .from(categories)
    .leftJoin(books, eq(books.categoryId, categories.id))
    .groupBy(categories.id)
    .orderBy(asc(categories.name));
}

export async function getAuthors() {
  const db = await getDb();
  return db
    .select({
      id: authors.id,
      fullname: authors.fullname,
      photo: authors.photo,
      biography: authors.biography,
      count: count(books.id),
    })
    .from(authors)
    .leftJoin(books, eq(books.authorId, authors.id))
    .groupBy(authors.id)
    .orderBy(asc(authors.fullname));
}

export async function getAuthor(id: number) {
  const db = await getDb();
  const rows = await db.select().from(authors).where(eq(authors.id, id)).limit(1);
  return rows[0];
}

export async function getNews(limit?: number) {
  const db = await getDb();
  const q = db.select().from(news).orderBy(sql`${news.publishedAt} DESC NULLS LAST`, desc(news.id));
  return limit ? q.limit(limit) : q;
}

export async function getNewsItem(id: number) {
  const db = await getDb();
  const rows = await db.select().from(news).where(eq(news.id, id)).limit(1);
  return rows[0];
}

export async function getGallery(limit?: number) {
  const db = await getDb();
  const q = db.select().from(gallery).orderBy(desc(gallery.id));
  return limit ? q.limit(limit) : q;
}

export async function getSettings() {
  let rows: (typeof settings.$inferSelect)[] = [];
  try {
    const db = await getDb();
    rows = await db.select().from(settings).where(eq(settings.id, 1)).limit(1);
  } catch {
    // database not reachable (e.g. while building) – fall back to defaults
  }
  return (
    rows[0] ?? {
      id: 1,
      phone: "",
      email: "",
      address: "",
      facebook: "",
      instagram: "",
      shippingPrice: "2",
      freeShippingFrom: "30",
    }
  );
}

export async function searchAll(q: string) {
  const like = `%${q}%`;
  const db = await getDb();
  const [bookRows, authorRows, newsRows] = await Promise.all([
    getBooks({ q }),
    db.select().from(authors).where(or(ilike(authors.fullname, like), ilike(authors.biography, like))).limit(12),
    db.select().from(news).where(or(ilike(news.title, like), ilike(news.content, like))).limit(12),
  ]);
  return { books: bookRows, authors: authorRows, news: newsRows };
}
