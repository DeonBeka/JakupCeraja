import Link from "next/link";
import { Layers, Pencil } from "lucide-react";
import { getCategoriesWithCount } from "@/lib/queries";
import { CategoryIcon, CATEGORY_ICON_OPTIONS } from "@/components/icons";
import { deleteCategory, saveCategory } from "../../actions";
import { ActionForm, ConfirmButton, SubmitButton } from "../../ui";
import { Flash } from "../flash";

export const metadata = { title: "Kategoritë" };

export default async function AdminCategories({ searchParams }: { searchParams: Promise<{ ok?: string; ndrysho?: string }> }) {
  const sp = await searchParams;
  const list = await getCategoriesWithCount();
  const editing = list.find((c) => String(c.id) === sp.ndrysho);

  return (
    <>
      <Flash ok={sp.ok} />
      <div className="grid-2">
        <section className="a-card">
          <div className="a-card-head">
            <h2>Kategoritë ({list.length})</h2>
          </div>
          {list.length ? (
            <div className="rows">
              {list.map((c) => (
                <div className="row" key={c.id}>
                  <div className="row-thumb round" style={{ background: "var(--green)", color: "#fff" }}>
                    <CategoryIcon name={c.icon} />
                  </div>
                  <div className="row-main">
                    <strong>{c.name}</strong>
                    <span>{Number(c.count) === 1 ? "1 libër" : `${c.count} libra`}</span>
                  </div>
                  <div className="row-actions">
                    <Link href={`/admin/kategorite?ndrysho=${c.id}`} className="a-icon-btn" aria-label="Ndrysho">
                      <Pencil />
                    </Link>
                    <ConfirmButton
                      action={deleteCategory.bind(null, c.id)}
                      message={`Ta fshij kategorinë "${c.name}"? Librat mbeten, por pa kategori.`}
                      className="a-icon-btn danger"
                    />
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="a-empty">
              <Layers />
              Ende nuk ka kategori.
            </div>
          )}
        </section>

        <section className="a-card">
          <div className="a-card-head">
            <h2>{editing ? `Ndrysho: ${editing.name}` : "Kategori e re"}</h2>
          </div>
          <ActionForm action={saveCategory.bind(null, editing?.id ?? 0)} key={editing?.id ?? "new"}>
            <div className="a-card-body a-fields">
              <label className="a-field full">
                Emri *
                <input type="text" name="name" required defaultValue={editing?.name} />
              </label>
              <label className="a-field full">
                Ikona
                <select name="icon" defaultValue={editing?.icon ?? "book"}>
                  {CATEGORY_ICON_OPTIONS.map((o) => (
                    <option key={o.value} value={o.value}>
                      {o.label}
                    </option>
                  ))}
                </select>
              </label>
            </div>
            <div className="a-actions">
              {editing ? (
                <Link href="/admin/kategorite" className="a-btn light">
                  Anulo
                </Link>
              ) : (
                <span />
              )}
              <SubmitButton>{editing ? "Ruaj" : "Shto kategorinë"}</SubmitButton>
            </div>
          </ActionForm>
        </section>
      </div>
    </>
  );
}
