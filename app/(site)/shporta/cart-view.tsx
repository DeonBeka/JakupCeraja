"use client";

import Link from "next/link";
import { useActionState, useEffect } from "react";
import { ArrowLeft, ArrowRight, Banknote, BookOpen, Check, CircleAlert, Minus, Plus, ShoppingBag, X } from "lucide-react";
import { useCart } from "@/components/cart";
import { money } from "@/lib/format";
import { placeOrder, type FormState } from "../actions";

export function CartView({ shippingPrice, freeFrom }: { shippingPrice: number; freeFrom: number }) {
  const { lines, ready, setQty, remove, clear } = useCart();
  const [state, action, pending] = useActionState<FormState, FormData>(placeOrder, {});

  useEffect(() => {
    if (state.ok) {
      clear();
      window.scrollTo({ top: 0, behavior: "smooth" });
    }
  }, [state.ok, clear]);

  if (state.ok) {
    return (
      <div className="success">
        <div className="success-icon">
          <Check />
        </div>
        <h2>Faleminderit, {state.name}!</h2>
        <p>Porosia juaj u pranua. Numri i porosisë:</p>
        <div className="order-code">{state.code}</div>
        <p>
          Totali: <strong>{money(state.total)}</strong>. Pagesa bëhet me para në dorë kur ta pranoni porosinë. Do t&apos;ju
          kontaktojmë së shpejti për konfirmim.
        </p>
        <Link href="/librat" className="btn btn-primary">
          Vazhdo me librat <ArrowRight />
        </Link>
      </div>
    );
  }

  if (!ready) return <div className="empty" style={{ minHeight: 240 }} />;

  if (lines.length === 0) {
    return (
      <div className="empty">
        <ShoppingBag />
        <h2>Shporta është bosh</h2>
        <p>Shfletoni librat tanë dhe shtoni ata që ju pëlqejnë.</p>
        <Link href="/librat" className="btn btn-primary">
          Shfleto librat <ArrowRight />
        </Link>
      </div>
    );
  }

  const subtotal = lines.reduce((s, l) => s + l.price * l.qty, 0);
  const shipping = subtotal >= freeFrom ? 0 : shippingPrice;
  const total = subtotal + shipping;
  const count = lines.reduce((s, l) => s + l.qty, 0);

  return (
    <div className="cart-layout">
      <div>
        <div className="cart-items">
          {lines.map((l) => (
            <div className="cart-item" key={l.id}>
              <Link href={`/librat/${l.id}`} className="cart-thumb">
                {l.cover ? <img src={l.cover} alt={l.title} /> : <BookOpen />}
              </Link>
              <div className="info">
                <h3>
                  <Link href={`/librat/${l.id}`}>{l.title}</Link>
                </h3>
                {l.author && <div className="muted">{l.author}</div>}
                <div className="unit">{money(l.price)}</div>
              </div>
              <div className="qty">
                <button type="button" onClick={() => setQty(l.id, l.qty - 1)} aria-label="Më pak">
                  <Minus />
                </button>
                <span>{l.qty}</span>
                <button type="button" onClick={() => setQty(l.id, l.qty + 1)} aria-label="Më shumë">
                  <Plus />
                </button>
              </div>
              <div className="line-total">{money(l.price * l.qty)}</div>
              <button type="button" className="remove-btn" onClick={() => remove(l.id)} aria-label="Hiq nga shporta">
                <X />
              </button>
            </div>
          ))}
        </div>
        <Link href="/librat" className="link-arrow" style={{ margin: "16px 4px 0" }}>
          <ArrowLeft /> Vazhdo blerjen
        </Link>

        <form action={action} className="card" id="porosia" style={{ marginTop: 26, scrollMarginTop: 100 }}>
          <h2>Të dhënat për dërgesë</h2>
          {state.error && (
            <div className="alert alert-error">
              <CircleAlert />
              <span>{state.error}</span>
            </div>
          )}
          <input type="hidden" name="cart" value={JSON.stringify(lines.map((l) => ({ id: l.id, qty: l.qty })))} />
          <div className="form-grid">
            <label className="field">
              Emri dhe mbiemri *
              <input name="fullname" required autoComplete="name" />
            </label>
            <label className="field">
              Telefoni *
              <input name="phone" type="tel" required autoComplete="tel" />
            </label>
            <label className="field full">
              Email *
              <input name="email" type="email" required autoComplete="email" />
            </label>
            <label className="field">
              Shteti
              <select name="country" defaultValue="Kosovë">
                {["Kosovë", "Shqipëri", "Maqedoni e Veriut", "Mal i Zi", "Tjetër"].map((c) => (
                  <option key={c}>{c}</option>
                ))}
              </select>
            </label>
            <label className="field">
              Qyteti *
              <input name="city" required autoComplete="address-level2" />
            </label>
            <label className="field full">
              Adresa *
              <input name="address" required autoComplete="street-address" />
            </label>
            <label className="field full">
              Shënim (opsional)
              <textarea name="note" rows={3} style={{ minHeight: 80 }} />
            </label>
          </div>
          <div className="pay-box">
            <Banknote />
            <div>
              <strong>Pagesa në dorëzim</strong>
              <span>Paguani me para në dorë kur ta pranoni porosinë.</span>
            </div>
          </div>
          <button className="btn btn-primary btn-block" type="submit" disabled={pending}>
            {pending ? "Duke dërguar porosinë…" : `Përfundo porosinë · ${money(total)}`}
          </button>
        </form>
      </div>

      <aside className="summary">
        <h2>Përmbledhja</h2>
        <div className="summary-row">
          <span>Librat ({count})</span>
          <span>{money(subtotal)}</span>
        </div>
        <div className="summary-row">
          <span>Dërgesa</span>
          <span>{shipping > 0 ? money(shipping) : "Falas"}</span>
        </div>
        {shipping > 0 && <p className="summary-hint">Dërgesë falas për porosi mbi {money(freeFrom)}.</p>}
        <div className="summary-row summary-total">
          <span>Totali</span>
          <span>{money(total)}</span>
        </div>
        <a href="#porosia" className="btn btn-primary btn-block" style={{ marginTop: 16 }}>
          Vazhdo te porosia
        </a>
      </aside>
    </div>
  );
}
