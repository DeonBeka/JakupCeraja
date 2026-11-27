import Link from "next/link";
import { ArrowRight, BookOpen } from "lucide-react";
import { CartProvider } from "@/components/cart";
import { Header } from "@/components/header";
import { Footer } from "@/components/footer";

export default function NotFound() {
  return (
    <CartProvider>
      <Header />
      <main>
        <section className="section">
          <div className="container">
            <div className="empty" style={{ maxWidth: 560, margin: "40px auto" }}>
              <BookOpen />
              <h2>Faqja nuk u gjet</h2>
              <p>Na vjen keq, faqja që kërkoni nuk ekziston ose është zhvendosur.</p>
              <Link href="/" className="btn btn-primary">
                Kthehu në ballinë <ArrowRight />
              </Link>
            </div>
          </div>
        </section>
      </main>
      <Footer />
    </CartProvider>
  );
}
