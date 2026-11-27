import Link from "next/link";
import { notFound } from "next/navigation";
import { eq } from "drizzle-orm";
import { ArrowLeft } from "lucide-react";
import { getDb } from "@/lib/db";
import { authors } from "@/lib/schema";
import { saveAuthor } from "../../../actions";
import { ActionForm, ImageField, SubmitButton } from "../../../ui";

export default async function EditAuthor({ params }: { params: Promise<{ id: string }> }) {
  const { id: raw } = await params;
  const isNew = raw === "i-ri";
  const id = isNew ? 0 : Number(raw);
  const db = await getDb();
  const author = isNew ? undefined : (await db.select().from(authors).where(eq(authors.id, id)))[0];
  if (!isNew && !author) notFound();

  return (
    <>
      <Link href="/admin/autoret" className="a-btn light" style={{ marginBottom: 16 }}>
        <ArrowLeft /> Kthehu te autorët
      </Link>
      <ActionForm action={saveAuthor.bind(null, id)}>
        <div className="a-form">
          <section className="a-card">
            <div className="a-card-head">
              <h2>{isNew ? "Autor i ri" : "Ndrysho autorin"}</h2>
            </div>
            <div className="a-card-body a-fields">
              <label className="a-field full">
                Emri i plotë *
                <input type="text" name="fullname" required defaultValue={author?.fullname} />
              </label>
              <label className="a-field full">
                Biografia
                <small>Lini një rresht bosh për paragraf të ri.</small>
                <textarea name="biography" rows={12} defaultValue={author?.biography ?? ""} />
              </label>
            </div>
            <div className="a-actions">
              <Link href="/admin/autoret" className="a-btn light">
                Anulo
              </Link>
              <SubmitButton>{isNew ? "Shto autorin" : "Ruaj ndryshimet"}</SubmitButton>
            </div>
          </section>
          <section className="a-card a-card-body">
            <ImageField name="photo" current={author?.photo ?? null} shape="square" label="Fotoja" />
          </section>
        </div>
      </ActionForm>
    </>
  );
}
