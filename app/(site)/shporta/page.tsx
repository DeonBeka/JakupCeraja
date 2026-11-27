import type { Metadata } from "next";
import { getSettings } from "@/lib/queries";
import { CartView } from "./cart-view";

export const metadata: Metadata = { title: "Shporta" };

export default async function CartPage() {
  const s = await getSettings();
  return (
    <>
      <section className="page-hero">
        <div className="container">
          <span className="tag">Porosia juaj</span>
          <h1>Shporta</h1>
        </div>
      </section>
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container">
          <CartView shippingPrice={Number(s.shippingPrice)} freeFrom={Number(s.freeShippingFrom)} />
        </div>
      </section>
    </>
  );
}
