import { BookGridSkeleton, PageHeroSkeleton } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <PageHeroSkeleton />
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container">
          <BookGridSkeleton count={8} />
        </div>
      </section>
    </>
  );
}
