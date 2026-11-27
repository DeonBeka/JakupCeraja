import type { Db } from "./db";
import { authors, books, categories, gallery, news, settings } from "./schema";

// Starting content copied from the old free.je website.
// Only added when the database is completely empty.
export async function seed(db: Db) {
  const cats = await db
    .insert(categories)
    .values([
      { name: "Histori", icon: "landmark" },
      { name: "Poezi", icon: "feather" },
      { name: "Përkthime", icon: "globe" },
      { name: "Ese", icon: "pen" },
      { name: "Roman", icon: "book" },
      { name: "Psikologji", icon: "brain" },
    ])
    .returning();
  const cat = (n: string) => cats.find((c) => c.name === n)!.id;

  const au = await db
    .insert(authors)
    .values([
      {
        fullname: "Fadil Kajtazi",
        photo: "/uploads/authors/author-2.jpg",
        biography:
          "Fadil Kajtazi është ekspert i çështjeve të sigurisë, publicist dhe autor nga Kosova. Ai ka lindur në lagjen e Muhaxhirëve në Gjilan, ku edhe ka vepruar e ka punuar gjatë jetës së tij.",
      },
      {
        fullname: "Brikenë Ceraja-Beka",
        photo: "/uploads/authors/author_1785449874_f836e80bf9.jpg",
        biography: [
          "Brikenë (Jakup) Ceraja–Beka u lind më 4 dhjetor 1979 në Mitrovicë ndërsa studimet për Gjuhë dhe Letërsi Shqipe në Fakultetin Filologjik në Universitetin e Prishtinës i kreu në vitin 2003. Ka mbaruar masterin në Letërsi Shqipe në Fakultetin Filologjik të Universitetit të Prishtinës me temën “Figura e Skënderbeut te ‘Skënderbeu i pafan’ e De Radës dhe ‘Histori e Skënderbeut’ e Naim Frashërit”. Aktualisht është duke vijuar doktoratën në Universitetin “Kliment Ohridski” të Sofjes.",
          "Me shkrime ka filluar të merret që në moshë të re. Shkruan kryesisht poezi, por herë pas here shkruan edhe tregime të shkurtra. Deri më tani ka botuar tri përmbledhje me poezi. Përmbledhja e parë, “Fytyra e babait”, u botua në vitin 2002 nga Shtëpia Botuese “Rozafa” në Prishtinë, ndërsa e dyta, “Udhëtim me fjalën e vrarë”, u botua në vitin 2006 nga Shtëpia Botuese “Faik Konica”, po ashtu në Prishtinë. Libri i saj i tretë, “Më fal pak dashuri”, u botua në vitin 2017 nga shtëpia botuese “Deon AB”.",
          "Ka bashkëpunuar me gazeta dhe revista letrare të Kosovës dhe Shqipërisë, ku ka botuar poezi, tregime të shkurtra, recensione dhe vështrime për libra dhe autorë të ndryshëm.",
          "Poezia e saj është vlerësuar si nga lexuesi po ashtu edhe nga kritika, duke u shpërblyer disa herë. Në vitin 2003, në manifestimin “Kepi i shpresës së mirë – Fahredin Gunga”, i cili mbahej në Mitrovicë, është shpërblyer me çmimin për veprën më të mirë në poezi me librin “Fytyra e babait”. Në vitin 2006, në Mitingun e poezisë në Gjakovë është shpërblyer me çmimin për poezinë më të mirë të lexuar në orën letrare të këtij manifestimi. Çmimin për poezinë më të mirë të lexuar në orën letrare të Mitingut të poezisë në Gjakovë e ka fituar edhe në vitin 2008. Me çmime është shpërblyer edhe nëpër aktivitete dhe konkurse të ndryshme letrare.",
          "Që nga viti 2003 është anëtare e Lidhjes së Shkrimtarëve të Kosovës.",
        ].join("\n\n"),
      },
      {
        fullname: "Leo Freundlich",
        photo: "/uploads/authors/author_1785450973_a182a8a26e.jpg",
        biography:
          "Leo Freundlich (23 prill 1875 – 12 shkurt 1953) ishte gazetar dhe politikan nga Austro-Hungaria dhe më vonë nga Austria, me përkatësi etnike gjermane dhe origjinë hebreje. Në fillim të shekullit të 20-të, ai ishte anëtar social-demokrat i Këshillit Perandorak. Në periudhën midis dy luftërave botërore, ai shërbeu si përfaqësues diplomatik i Shqipërisë në Austri.",
      },
      {
        fullname: "Faruk Shehiq",
        photo: null,
        biography: [
          "Faruk Shehiq lindi në vitin 1970 në Bihaq, në Republikën Federale Socialiste të Jugosllavisë. Deri në shpërthimin e luftës në vitin 1992, ai studioi mjekësi veterinare në Zagreb. Megjithatë, në moshën 22-vjeçare iu bashkua Ushtrisë së Bosnjë dhe Hercegovinës. Në pranverën e vitit 1994 ai u plagos rëndë në këmbën e majtë. Gjashtë muaj më vonë u kthye në detyrën e tij të vjetër - komandant toge. Në vjeshtën e vitit 1995 udhëhoqi një njësi prej 130 burrash. Pas luftës iu drejtua letërsisë.",
          "Libri i tij i dytë “Hit Depot” (2003) ishte bestseller letrar në Bosnjë, pavarësisht faktit se ishte një libër me poezi. Në këtë libër ai bëri një skicë të disa temave kryesore që do t'i përdorë në veprat e tij të mëvonshme, siç është jeta pas luftës në skaj të shoqërisë.",
          "\"Libri mbi Unën\" (2011), përkthyer në shqip nga Fadil Bajraj dhe i botuar nga Shtëpia Botuese “Jakup Ceraja”, është libër për personin që dëshiron të kapërcejë traumën e tij personale të ndërprerjes së kohës dhe hapësirës të shkaktuar nga lufta 1992-1995 në Bosnjë dhe Hercegovinë. Është gjithashtu roman për fëmijërinë pranë lumit të bukur, për peshqit, bimët, notin, zhytjen dhe shijimin e jetës në një qytet të vogël boshnjak. Me këtë roman autori e ka fituar Çmimin e Letërsisë të Bashkimit Evropian (2013) dhe Çmimin “Meša Selimović” (2011).",
          "Librat e tij janë përkthyer në anglisht, frëngjisht, gjermanisht, bullgarisht, rumanisht, spanjisht, holandisht, maqedonisht, polonisht, turqisht, sllovenisht, italisht, shqip, ukrainisht, arabisht dhe hungarisht. Shehiq jeton në Sarajevë dhe punon si kolumnist dhe gazetar në revistën e respektuar politike BH Dani dhe radiosarajevo.ba.",
        ].join("\n\n"),
      },
      {
        fullname: "Luis Kuperus",
        photo: "/uploads/authors/author_1785534170_0879f82f67.jpg",
        biography: [
          "Louis Marie Anne Couperus ishte një figurë kyçe në letërsinë holandeze, i njohur për kontributet e tij në skenën letrare evropiane në fillim të shekullit XX. I ndikuar nga realizmi social i Emile Zolës, veprat e Kuperusit shpesh eksplorojnë ndërveprimin midis jetës personale dhe fatit, duke trajtuar tema të thella filozofike. Romanet e tij të shquara përfshijnë “Eline Vere”, i cili përshkruan problemet e një gruaje me trashëgiminë e saj, “Të moshuarit dhe gjërat që kalojnë”, ku pasojat e një krimi të harruar varen mbi brezat e ardhshëm.",
          "Jeta e hershme e Kuperusit në Java, ku babai i tij ishte zyrtar qeveritar, formësoi ndjeshëm stilin e tij narrativ, me shumë tema që pasqyrojnë prejardhjen e tij koloniale. Ndër projektet e tij më ambicioze janë “Librat e Shpirtrave të Vegjël”, një sagë gjithëpërfshirëse familjare, dhe një seri romanesh historike që shqyrtojnë fatet e figurave si Heliogabalus dhe Aleksandri i Madh.",
          "Në gjuhën shqipe tashmë janë të përkthyer dy romane të tij, “Fati” dhe “Psika”, të përkthyera nga Alfred Beka.",
        ].join("\n\n"),
      },
    ])
    .returning();
  const aut = (n: string) => au.find((a) => a.fullname === n)!.id;

  await db.insert(books).values([
    {
      title: "Farsat historike serbe",
      price: "0",
      authorId: aut("Fadil Kajtazi"),
      categoryId: cat("Histori"),
      cover: "/images/books/book_1785505332_b4ff132c.jpg",
      description: "Farsat historike serbe",
      publishDate: "2026-09-15",
      featured: true,
    },
    {
      title: "Kambana e Heshtjes",
      price: "0",
      authorId: aut("Brikenë Ceraja-Beka"),
      categoryId: cat("Poezi"),
      cover: "/images/books/book_1785451265_3dea111f.jpg",
      description: "Përmbledhje me poezi nga Brikenë Ceraja-Beka.",
      publishDate: "2021-06-19",
      featured: true,
    },
    {
      title: "Golgota Shqiptare 1912-1913",
      price: "0",
      authorId: aut("Leo Freundlich"),
      categoryId: cat("Histori"),
      cover: "/images/books/book_1785431903_c1969f17.jpg",
      description: "Dëshmi e gazetarit Leo Freundlich për ngjarjet e viteve 1912-1913.",
      publishDate: "2025-07-18",
      featured: true,
    },
    {
      title: "Kisha ortodokse serbe dhe fashizmi",
      price: "0",
      authorId: aut("Fadil Kajtazi"),
      categoryId: cat("Histori"),
      cover: "/images/books/book_1785434417_80487de9.jpg",
      description: "Libër nga Fadil Kajtazi.",
      publishDate: "2026-01-15",
      featured: false,
    },
  ]);

  await db.insert(gallery).values([
    { image: "/uploads/gallery/gallery_1785501408_8cb9eba610.jpg", title: "Ftesë" },
    { image: "/uploads/gallery/gallery-6.jpg", title: "Panairi i Librit" },
    { image: "/uploads/gallery/gallery-5.jpg", title: "Takim me Lexues" },
    { image: "/uploads/gallery/gallery-4.jpg", title: "Botimet Tona" },
    { image: "/uploads/gallery/gallery-3.jpg", title: "Event Letrar" },
    { image: "/uploads/gallery/gallery-2.jpg", title: "Panair i Librit" },
    { image: "/uploads/gallery/gallery-1.jpg", title: "Promovim Libri" },
  ]);

  await db.insert(news).values({
    title: "Panair Libri në Vlorë",
    category: "Kulturë",
    excerpt: 'Shtëpia Botuese "Jakup Ceraja" do të marrë pjesë në panairin e librit në Vlorë.',
    content: 'Shtëpia Botuese "Jakup Ceraja" do të marrë pjesë në panairin e librit në Vlorë. Ju ftojmë të na vizitoni në stendën tonë.',
    publishedAt: "2026-07-31",
  });

  await db
    .insert(settings)
    .values({
      id: 1,
      phone: "+383 00 000 000",
      email: "info@jakupceraja.com",
      address: "Prishtinë, Kosovë",
      facebook: "",
      instagram: "",
    })
    .onConflictDoNothing();
}
