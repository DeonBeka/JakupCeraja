"use server";

import { inArray, eq } from "drizzle-orm";
import { randomBytes } from "node:crypto";
import { getDb } from "@/lib/db";
import { books, messages, orderItems, orders } from "@/lib/schema";
import { getSettings } from "@/lib/queries";

export type FormState = { ok?: boolean; error?: string; code?: string; total?: number; name?: string };

const str = (fd: FormData, k: string, max = 500) => String(fd.get(k) ?? "").trim().slice(0, max);

export async function sendMessage(_: FormState, fd: FormData): Promise<FormState> {
  if (str(fd, "website")) return { ok: true }; // spam trap
  const fullname = str(fd, "fullname", 120);
  const email = str(fd, "email", 160);
  const message = str(fd, "message", 5000);
  if (!fullname || !message) return { error: "Plotësoni emrin dhe mesazhin." };
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return { error: "Shkruani një email të vlefshëm." };
  const db = await getDb();
  await db.insert(messages).values({ fullname, email, message, phone: str(fd, "phone", 60), subject: str(fd, "subject", 200) });
  return { ok: true };
}

export async function placeOrder(_: FormState, fd: FormData): Promise<FormState> {
  let cart: { id: number; qty: number }[] = [];
  try {
    cart = JSON.parse(String(fd.get("cart") ?? "[]"));
  } catch {}
  cart = cart
    .map((l) => ({ id: Number(l.id), qty: Math.max(1, Math.min(20, Math.floor(Number(l.qty)))) }))
    .filter((l) => Number.isInteger(l.id) && l.id > 0 && Number.isFinite(l.qty));
  if (!cart.length) return { error: "Shporta është bosh." };

  const fullname = str(fd, "fullname", 120);
  const email = str(fd, "email", 160);
  const phone = str(fd, "phone", 60);
  const city = str(fd, "city", 100);
  const address = str(fd, "address", 250);
  const country = str(fd, "country", 80) || "Kosovë";
  const note = str(fd, "note", 1000);
  if (!fullname) return { error: "Shkruani emrin dhe mbiemrin." };
  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return { error: "Shkruani një email të vlefshëm." };
  if (phone.replace(/\D/g, "").length < 6) return { error: "Shkruani një numër telefoni të vlefshëm." };
  if (!city || !address) return { error: "Shkruani qytetin dhe adresën." };

  const db = await getDb();
  const rows = await db.select().from(books).where(inArray(books.id, cart.map((l) => l.id)));
  const lines = cart
    .map((l) => ({ ...l, book: rows.find((b) => b.id === l.id) }))
    .filter((l) => l.book && Number(l.book.price) > 0 && l.book.inStock);
  if (!lines.length) return { error: "Librat në shportë nuk janë më në dispozicion." };

  const s = await getSettings();
  const subtotal = lines.reduce((sum, l) => sum + Number(l.book!.price) * l.qty, 0);
  const shipping = subtotal >= Number(s.freeShippingFrom) ? 0 : Number(s.shippingPrice);
  const total = subtotal + shipping;
  const code = "JC-" + randomBytes(3).toString("hex").toUpperCase();

  const [order] = await db
    .insert(orders)
    .values({
      code,
      fullname,
      email,
      phone,
      country,
      city,
      address,
      note,
      subtotal: subtotal.toFixed(2),
      shipping: shipping.toFixed(2),
      total: total.toFixed(2),
    })
    .returning({ id: orders.id });

  try {
    await db.insert(orderItems).values(
      lines.map((l) => ({
        orderId: order.id,
        bookId: l.book!.id,
        title: l.book!.title,
        cover: l.book!.cover,
        unitPrice: Number(l.book!.price).toFixed(2),
        quantity: l.qty,
      })),
    );
  } catch {
    await db.delete(orders).where(eq(orders.id, order.id));
    return { error: "Porosia nuk u ruajt. Ju lutem provoni përsëri." };
  }

  return { ok: true, code, total, name: fullname };
}
