import Link from "next/link";
import { notFound } from "next/navigation";
import { asc, eq } from "drizzle-orm";
import { ArrowLeft } from "lucide-react";
import { getDb } from "@/lib/db";
import { authors, books, categories } from "@/lib/schema";
import { saveBook } from "../../../actions";
import { ActionForm, ImageField, SubmitButton } from "../../../ui";

export default async function EditBook({ params }: { params: Promise<{ id: string }> }) {
  const { id: raw } = await params;
  const isNew = raw === "i-ri";
  const id = isNew ? 0 : Number(raw);
  const db = await getDb();
  const book = isNew ? undefined : (await db.select().from(books).where(eq(books.id, id)))[0];
  if (!isNew && !book) notFound();
  const [authorList, catList] = await Promise.all([
    db.select({ id: authors.id, name: authors.fullname }).from(authors).orderBy(asc(authors.fullname)),
    db.select({ id: categories.id, name: categories.name }).from(categories).orderBy(asc(categories.name)),
  ]);

  return (
    <>
      <Link href="/admin/librat" className="a-btn light" style={{ marginBottom: 16 }}>
        <ArrowLeft /> Kthehu te librat
      </Link>
      <ActionForm action={saveBook.bind(null, id)}>
        <div className="a-form">
          <section className="a-card">
            <div className="a-card-head">
              <h2>{isNew ? "Libër i ri" : "Ndrysho librin"}</h2>
            </div>
            <div className="a-card-body a-fields">
              <label className="a-field full">
                Titulli *
                <input type="text" name="title" required defaultValue={book?.title} />
              </label>
              <label className="a-field">
                Çmimi (€) *<small>Vendosni 0 nëse çmimi nuk dihet ende – libri nuk mund të blihet.</small>
                <input type="number" name="price" min="0" step="0.01" inputMode="decimal" defaultValue={book ? Number(book.price) : ""} required />
              </label>
              <label className="a-field">
                Data e botimit
                <input type="date" name="publishDate" defaultValue={book?.publishDate ?? ""} />
              </label>
              <label className="a-field">
                Autori
                <select name="authorId" defaultValue={book?.authorId ?? ""}>
                  <option value="">— Zgjidh autorin —</option>
                  {authorList.map((a) => (
                    <option key={a.id} value={a.id}>
                      {a.name}
                    </option>
                  ))}
                </select>
                <small>
                  Mungon autori? <Link href="/admin/autoret/i-ri" style={{ textDecoration: "underline" }}>Shtoni një autor</Link>
                </small>
              </label>
              <label className="a-field">
                Kategoria
                <select name="categoryId" defaultValue={book?.categoryId ?? ""}>
                  <option value="">— Zgjidh kategorinë —</option>
                  {catList.map((c) => (
                    <option key={c.id} value={c.id}>
                      {c.name}
                    </option>
                  ))}
                </select>
              </label>
              <label className="a-field full">
                Përshkrimi
                <small>Lini një rresht bosh për paragraf të ri.</small>
                <textarea name="description" rows={8} defaultValue={book?.description ?? ""} />
              </label>
              <label className="a-check">
                <input type="checkbox" name="featured" defaultChecked={book?.featured ?? false} /> Shfaqe te &quot;Librat e veçuar&quot; në ballinë
              </label>
              <label className="a-check">
                <input type="checkbox" name="inStock" defaultChecked={book?.inStock ?? true} /> Në stok (mund të blihet)
              </label>
            </div>
            <div className="a-actions">
              <Link href="/admin/librat" className="a-btn light">
                Anulo
              </Link>
              <SubmitButton>{isNew ? "Shto librin" : "Ruaj ndryshimet"}</SubmitButton>
            </div>
          </section>
          <section className="a-card a-card-body">
            <ImageField name="cover" current={book?.cover ?? null} label="Kopertina" />
          </section>
        </div>
      </ActionForm>
    </>
  );
}
