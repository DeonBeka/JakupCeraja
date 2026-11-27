import { CardGridSkeleton, PageHeroSkeleton } from "@/components/skeletons";

export default function Loading() {
  return (
    <>
      <PageHeroSkeleton />
      <section className="section" style={{ paddingTop: 50 }}>
        <div className="container">
          <CardGridSkeleton count={6} media="16 / 9" />
        </div>
      </section>
    </>
  );
}
