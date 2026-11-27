"use client";

import { useActionState } from "react";
import { CircleAlert } from "lucide-react";
import { login, type ActionState } from "../actions";

export function LoginForm({ setup }: { setup: boolean }) {
  const [state, action, pending] = useActionState<ActionState, FormData>(login, {});
  return (
    <form action={action}>
      {state.error && (
        <div className="a-alert err">
          <CircleAlert /> {state.error}
        </div>
      )}
      <input type="hidden" name="setup" value={setup ? "1" : ""} />
      {setup && (
        <label className="a-field">
          Emri i plotë
          <input type="text" name="fullname" required />
        </label>
      )}
      <label className="a-field">
        Username
        <input type="text" name="username" required autoComplete="username" autoCapitalize="none" />
      </label>
      <label className="a-field">
        Password {setup && <small>(të paktën 8 karaktere)</small>}
        <input type="password" name="password" required minLength={setup ? 8 : undefined} autoComplete={setup ? "new-password" : "current-password"} />
      </label>
      <button className="a-btn" type="submit" disabled={pending}>
        {pending ? "Duke u kyçur…" : setup ? "Krijo llogarinë" : "Kyçu"}
      </button>
    </form>
  );
}
