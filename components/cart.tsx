"use client";

import { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";
import Link from "next/link";
import { CircleCheck, ShoppingBag } from "lucide-react";

export type CartLine = { id: number; title: string; price: number; cover: string | null; author: string | null; qty: number };

type CartCtx = {
  lines: CartLine[];
  count: number;
  ready: boolean;
  add: (line: Omit<CartLine, "qty">, qty?: number) => void;
  setQty: (id: number, qty: number) => void;
  remove: (id: number) => void;
  clear: () => void;
};

const Ctx = createContext<CartCtx | null>(null);
const KEY = "jc_cart_v1";

export function CartProvider({ children }: { children: React.ReactNode }) {
  const [lines, setLines] = useState<CartLine[]>([]);
  const [ready, setReady] = useState(false);
  const [toast, setToast] = useState<string | null>(null);

  useEffect(() => {
    try {
      const saved = JSON.parse(localStorage.getItem(KEY) || "[]");
      if (Array.isArray(saved)) setLines(saved);
    } catch {}
    setReady(true);
  }, []);

  useEffect(() => {
    if (ready) localStorage.setItem(KEY, JSON.stringify(lines));
  }, [lines, ready]);

  useEffect(() => {
    if (!toast) return;
    const t = setTimeout(() => setToast(null), 3500);
    return () => clearTimeout(t);
  }, [toast]);

  const add = useCallback((line: Omit<CartLine, "qty">, qty = 1) => {
    setLines((cur) => {
      const found = cur.find((l) => l.id === line.id);
      if (found) return cur.map((l) => (l.id === line.id ? { ...l, ...line, qty: Math.min(20, l.qty + qty) } : l));
      return [...cur, { ...line, qty }];
    });
    setToast(`"${line.title}" u shtua në shportë.`);
  }, []);

  const setQty = useCallback((id: number, qty: number) => {
    setLines((cur) => (qty <= 0 ? cur.filter((l) => l.id !== id) : cur.map((l) => (l.id === id ? { ...l, qty: Math.min(20, qty) } : l))));
  }, []);

  const remove = useCallback((id: number) => setLines((cur) => cur.filter((l) => l.id !== id)), []);
  const clear = useCallback(() => setLines([]), []);

  const value = useMemo(
    () => ({ lines, count: lines.reduce((s, l) => s + l.qty, 0), ready, add, setQty, remove, clear }),
    [lines, ready, add, setQty, remove, clear],
  );

  return (
    <Ctx.Provider value={value}>
      {children}
      {toast && (
        <div className="toast" role="status">
          <CircleCheck />
          <span>{toast}</span>
          <Link href="/shporta">Shiko shportën</Link>
        </div>
      )}
    </Ctx.Provider>
  );
}

export function useCart() {
  const c = useContext(Ctx);
  if (!c) throw new Error("useCart must be used inside CartProvider");
  return c;
}

export function AddToCart({
  book,
  large = false,
}: {
  book: { id: number; title: string; price: number; cover: string | null; author: string | null; inStock?: boolean };
  large?: boolean;
}) {
  const { add } = useCart();
  const [added, setAdded] = useState(false);
  const available = book.price > 0 && book.inStock !== false;

  if (!available) {
    return large ? (
      <button className="btn btn-primary" disabled>
        {book.price > 0 ? "Jashtë stokut" : "Çmimi së shpejti"}
      </button>
    ) : null;
  }

  return (
    <button
      type="button"
      className={large ? "btn btn-primary" : `add-btn${added ? " added" : ""}`}
      onClick={() => {
        add({ id: book.id, title: book.title, price: book.price, cover: book.cover, author: book.author });
        setAdded(true);
        setTimeout(() => setAdded(false), 1500);
      }}
      aria-label={`Shto "${book.title}" në shportë`}
    >
      <ShoppingBag />
      <span className="label">{large ? "Shto në shportë" : added ? "U shtua" : "Shto"}</span>
    </button>
  );
}
