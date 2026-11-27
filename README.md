# Shtëpia Botuese "Jakup Ceraja"

Website and online bookshop: https://shbjakupceraja.vercel.app

Built with Next.js, hosted on Vercel. The database is Postgres (Neon) and uploaded images go to Vercel Blob.

## Hosting on Vercel (one-time setup)

1. **Database:** Vercel → project **shbjakupceraja** → **Storage** → **Create Database** → **Neon (Postgres)**,
   region **Frankfurt**, and connect it to this project. This adds `DATABASE_URL` automatically.
   Tables and starting content are created on the first visit.
2. **Images:** a Blob store is already connected (`BLOB_READ_WRITE_TOKEN`).
3. **Admin login:** `ADMIN_USERNAME` / `ADMIN_PASSWORD` in Environment Variables are the first login.
   After logging in at `/admin`, set your own password in **Cilësimet**. From then on that password is used.
4. Redeploy after changing environment variables.

## Admin

Go to `/admin`. On a phone, use the ☰ button for the menu.

- **Porositë** – new orders, customer phone/address, change status (E re → Konfirmuar → Dërguar → Përfunduar)
- **Librat** – add/edit books, price, cover, "I veçuar" (shown on the homepage), in stock
- **Autorët, Kategoritë, Lajmet, Galeria** – manage the content
- **Mesazhet** – messages from the contact page
- **Cilësimet** – phone, email, address, social links, delivery price, your password

A book with price 0 shows "Çmimi së shpejti" and cannot be added to the cart.

## Running locally

```
npm install
npm run dev
```

Open http://localhost:3000. Without `DATABASE_URL` a local database is created in `node_modules/.cache/jc-pglite`.
Copy `.env.example` to `.env.local` and set `ADMIN_USERNAME` / `ADMIN_PASSWORD` to log in to the admin.
