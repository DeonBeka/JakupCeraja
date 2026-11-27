import type { Metadata } from "next";
import { count, eq } from "drizzle-orm";
import { requireAdmin } from "@/lib/auth";
import { getDb } from "@/lib/db";
import { messages, orders } from "@/lib/schema";
import { AdminShell } from "./shell";
import "../admin.css";

export const metadata: Metadata = { title: { default: "Admin", template: "%s | Admin" }, robots: { index: false } };
export const dynamic = "force-dynamic";

export default async function PanelLayout({ children }: { children: React.ReactNode }) {
  const admin = await requireAdmin();
  const db = await getDb();
  const [[newOrders], [unread]] = await Promise.all([
    db.select({ c: count() }).from(orders).where(eq(orders.status, "new")),
    db.select({ c: count() }).from(messages).where(eq(messages.isRead, false)),
  ]);
  return (
    <AdminShell name={admin.name} newOrders={Number(newOrders.c)} unread={Number(unread.c)}>
      {children}
    </AdminShell>
  );
}
