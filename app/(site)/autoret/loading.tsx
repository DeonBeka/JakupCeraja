import { CardGridSkeleton, PageHeroSkeleton } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <PageHeroSkeleton />
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container">
          <CardGridSkeleton count={6} media="1 / 1" grid="authors-grid" />
        </div>
      </section>
    </>
  );
}
