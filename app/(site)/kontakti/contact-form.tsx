"use client";

import { useActionState } from "react";
import { ArrowRight, CircleAlert, CircleCheck } from "lucide-react";
import { sendMessage, type FormState } from "../actions";

export function ContactForm() {
  const [state, action, pending] = useActionState<FormState, FormData>(sendMessage, {});

  if (state.ok) {
    return (
      <div className="alert alert-ok">
        <CircleCheck />
        <span>Faleminderit! Mesazhi juaj u dërgua. Do t&apos;ju përgjigjemi së shpejti.</span>
      </div>
    );
  }

  return (
    <form action={action}>
      {state.error && (
        <div className="alert alert-error">
          <CircleAlert />
          <span>{state.error}</span>
        </div>
      )}
      <input type="text" name="website" tabIndex={-1} autoComplete="off" style={{ position: "absolute", left: -9999 }} aria-hidden="true" />
      <div className="form-grid">
        <label className="field">
          Emri dhe mbiemri *
          <input name="fullname" required autoComplete="name" />
        </label>
        <label className="field">
          Email *
          <input name="email" type="email" required autoComplete="email" />
        </label>
        <label className="field">
          Telefoni
          <input name="phone" type="tel" autoComplete="tel" />
        </label>
        <label className="field">
          Subjekti
          <input name="subject" />
        </label>
        <label className="field full">
          Mesazhi *
          <textarea name="message" required rows={5} />
        </label>
      </div>
      <button className="btn btn-primary" type="submit" disabled={pending} style={{ marginTop: 18 }}>
        {pending ? "Duke dërguar…" : "Dërgo mesazhin"} <ArrowRight />
      </button>
    </form>
  );
}
