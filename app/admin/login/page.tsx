import type { Metadata } from "next";
import Link from "next/link";
import { redirect } from "next/navigation";
import { getSession, needsSetup } from "@/lib/auth";
import { LoginForm } from "./login-form";
import "../admin.css";

export const metadata: Metadata = { title: "Hyrja e administratorit", robots: { index: false } };
export const dynamic = "force-dynamic";

export default async function LoginPage() {
  if (await getSession()) redirect("/admin");
  const setup = await needsSetup();
  return (
    <div className="login-wrap">
      <div className="login-card">
        <img src="/images/logo/logo.png" alt="Jakup Ceraja" width={528} height={372} />
        <h1>{setup ? "Mirë se vini!" : "Hyrja e administratorit"}</h1>
        <p>{setup ? "Krijoni llogarinë e administratorit. Kjo bëhet vetëm një herë." : "Kyçuni për të menaxhuar faqen."}</p>
        <LoginForm setup={setup} />
        <Link href="/" className="login-back">
          ← Kthehu në faqe
        </Link>
      </div>
    </div>
  );
}
