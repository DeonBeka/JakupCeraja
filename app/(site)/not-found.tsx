import Link from "next/link";
import { ArrowRight, BookOpen } from "lucide-react";

export default function NotFound() {
  return (
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
  );
}
