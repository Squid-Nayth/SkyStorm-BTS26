from __future__ import annotations

from datetime import date
from html import escape
from pathlib import Path

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.pdfbase import pdfmetrics
from reportlab.pdfbase.ttfonts import TTFont
from reportlab.platypus import (
    PageBreak,
    Paragraph,
    Preformatted,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path("/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26")
OUTPUT = ROOT / "output/pdf/skystorm-technical-document.pdf"
TMP = ROOT / "tmp/pdfs"


def register_fonts() -> None:
    font_dir = Path("/System/Library/Fonts/Supplemental")
    pdfmetrics.registerFont(TTFont("Arial", str(font_dir / "Arial.ttf")))
    pdfmetrics.registerFont(TTFont("Arial-Bold", str(font_dir / "Arial Bold.ttf")))
    pdfmetrics.registerFont(TTFont("Arial-Italic", str(font_dir / "Arial Italic.ttf")))


register_fonts()

styles = getSampleStyleSheet()
styles.add(
    ParagraphStyle(
        name="DocTitle",
        parent=styles["Title"],
        fontName="Arial-Bold",
        fontSize=24,
        leading=28,
        alignment=TA_CENTER,
        textColor=colors.HexColor("#1f2937"),
        spaceAfter=14,
    )
)
styles.add(
    ParagraphStyle(
        name="DocSubTitle",
        parent=styles["Normal"],
        fontName="Arial",
        fontSize=11,
        leading=16,
        alignment=TA_CENTER,
        textColor=colors.HexColor("#4b5563"),
        spaceAfter=8,
    )
)
styles.add(
    ParagraphStyle(
        name="SectionTitle",
        parent=styles["Heading1"],
        fontName="Arial-Bold",
        fontSize=18,
        leading=22,
        textColor=colors.HexColor("#1f2937"),
        spaceBefore=10,
        spaceAfter=10,
    )
)
styles.add(
    ParagraphStyle(
        name="SubSectionTitle",
        parent=styles["Heading2"],
        fontName="Arial-Bold",
        fontSize=13,
        leading=17,
        textColor=colors.HexColor("#2563eb"),
        spaceBefore=8,
        spaceAfter=6,
    )
)
styles.add(
    ParagraphStyle(
        name="Body",
        parent=styles["Normal"],
        fontName="Arial",
        fontSize=10,
        leading=15,
        alignment=TA_JUSTIFY,
        textColor=colors.HexColor("#111827"),
        spaceAfter=6,
    )
)
styles.add(
    ParagraphStyle(
        name="BodySmall",
        parent=styles["Normal"],
        fontName="Arial",
        fontSize=8.5,
        leading=12,
        alignment=TA_LEFT,
        textColor=colors.HexColor("#111827"),
        spaceAfter=4,
    )
)
styles.add(
    ParagraphStyle(
        name="FeatureTitle",
        parent=styles["Heading2"],
        fontName="Arial-Bold",
        fontSize=12,
        leading=16,
        textColor=colors.HexColor("#111827"),
        spaceBefore=8,
        spaceAfter=4,
    )
)
styles.add(
    ParagraphStyle(
        name="CodeLine",
        parent=styles["Normal"],
        fontName="Courier",
        fontSize=8.5,
        leading=11,
        textColor=colors.HexColor("#374151"),
        leftIndent=10,
        spaceAfter=2,
    )
)


def p(text: str, style: str = "Body") -> Paragraph:
    return Paragraph(text, styles[style])


def code_ref(label: str, path: str, lines: str) -> Paragraph:
    text = f"<b>{escape(label)} :</b> <font name='Courier'>{escape(path)}:{escape(lines)}</font>"
    return Paragraph(text, styles["BodySmall"])


def add_header_footer(canvas, doc) -> None:
    canvas.saveState()
    canvas.setFont("Arial", 8)
    canvas.setFillColor(colors.HexColor("#6b7280"))
    canvas.drawString(doc.leftMargin, 20, "SkyStorm - document technique")
    canvas.drawRightString(A4[0] - doc.rightMargin, 20, f"Page {doc.page}")
    canvas.restoreState()


important_tree = """SkyStorm-BTS26/
|- app/
|  |- Http/Controllers/      -> logique metier des routes
|  |- Models/                -> relations avec les tables
|- database/
|  |- migrations/            -> structure SQL du projet
|  |- factories/             -> objets de test
|- resources/views/          -> pages Blade (interface)
|- routes/web.php            -> declaration des URL
|- tests/Feature/            -> tests fonctionnels
|- public/                   -> assets publics
|- output/pdf/               -> PDF final genere
`- tmp/pdfs/                 -> fichiers temporaires pour la generation PDF"""


database_rows = [
    ["Table", "Role", "Migration"],
    ["users", "Comptes, profil, statut admin, avatar, bio", "create_users + add_social_fields + add_profile_fields"],
    ["posts", "Publications du reseau social", "2026_02_12_185401_create_posts_table.php"],
    ["followers", "Relation abonnement abonne / suivi", "2026_03_19_164551_create_followers_table.php"],
    ["notes", "Notes privees de l utilisateur", "2026_03_19_000000_create_notes_table.php"],
    ["post_likes", "Likes sur les publications", "2026_06_29_120100_create_post_likes_table.php"],
    ["favorite_posts", "Publications mises en favoris", "2026_06_29_120200_create_favorite_posts_table.php"],
    ["comments", "Commentaires sur un post", "2026_06_29_120300_create_comments_table.php"],
    ["post_reports", "Signalements et moderation", "2026_06_29_120400_create_post_reports_table.php"],
    ["messages", "Messages prives entre deux utilisateurs", "2026_06_29_120600_create_messages_table.php"],
    ["sessions", "Sessions Laravel des utilisateurs connectes", "0001_01_01_000000_create_users_table.php"],
]


route_rows = [
    ["Zone", "Exemples", "Ou regarder"],
    ["Invites", "/, /explore, /members", "routes/web.php:14-23"],
    ["Authentification", "login, register, reset password", "routes/web.php:25 et resources/views/auth/"],
    ["Accueil connecte", "/home", "routes/web.php:27, app/Http/Controllers/HomeController.php:15-49"],
    ["Posts", "/posts, /posts/create", "routes/web.php:29-35, app/Http/Controllers/PostController.php:15-80"],
    ["Profils", "/users/{user}, /profile/edit", "routes/web.php:20-22 et 44-45"],
    ["Interactions", "likes, commentaires, favoris, signalements", "routes/web.php:41-60"],
    ["Messagerie", "/messages, /messages/{user}", "routes/web.php:47-49"],
    ["Administration", "/admin/reports", "routes/web.php:62-63"],
]


features = [
    {
        "title": "1. Inscription d un utilisateur",
        "category": "Authentification",
        "summary": "Laravel UI fournit le formulaire, la validation et la creation du compte. Le projet ajoute une petite regle utile : le tout premier compte cree devient administrateur.",
        "related": "Cette fonctionnalite est liee a la connexion, au profil utilisateur et a la moderation admin.",
        "refs": [
            ("Routes auth", "routes/web.php", "25"),
            ("Controleur", "app/Http/Controllers/Auth/RegisterController.php", "49-71"),
            ("Vue", "resources/views/auth/register.blade.php", "11-71"),
        ],
    },
    {
        "title": "2. Connexion et deconnexion",
        "category": "Authentification",
        "summary": "Un utilisateur se connecte avec son email et son mot de passe. La session est ensuite geree par Laravel, puis les pages protegees deviennent accessibles.",
        "related": "Toutes les autres fonctionnalites sociales importantes dependent de la connexion.",
        "refs": [
            ("Routes auth", "routes/web.php", "25"),
            ("Vue login", "resources/views/auth/login.blade.php", "11-67"),
            ("Bouton logout", "resources/views/layouts/app.blade.php", "132-136"),
        ],
    },
    {
        "title": "3. Tableau de bord apres connexion",
        "category": "Accueil",
        "summary": "La page d accueil connectee centralise le fil d actualite, les statistiques, les notes recentes, les favoris et les suggestions de profils a suivre.",
        "related": "C est le point d entree principal du projet une fois l utilisateur connecte.",
        "refs": [
            ("Route", "routes/web.php", "27"),
            ("Controleur", "app/Http/Controllers/HomeController.php", "15-49"),
            ("Vue accueil", "resources/views/home.blade.php", "4-159"),
        ],
    },
    {
        "title": "4. Affichage des statistiques personnelles",
        "category": "Accueil",
        "summary": "Le projet calcule le nombre d abonnes, de publications, de likes recus, le meilleur score d un post et le nombre de favoris. C est directement exploitable pour un sujet d examen sur les agregats.",
        "related": "Lie au modele User, aux posts, aux likes et a la page d accueil.",
        "refs": [
            ("Calcul des stats", "app/Http/Controllers/HomeController.php", "39-47"),
            ("Affichage", "resources/views/home.blade.php", "9-40"),
        ],
    },
    {
        "title": "5. Creation d une publication",
        "category": "Posts",
        "summary": "L utilisateur saisit un texte court, le formulaire verifie la taille maximale puis le post est enregistre avec l identifiant du proprietaire.",
        "related": "Cette fonctionnalite sert ensuite aux likes, commentaires, favoris, signalements et au profil.",
        "refs": [
            ("Route resource", "routes/web.php", "33-35"),
            ("Controleur store", "app/Http/Controllers/PostController.php", "38-46"),
            ("Formulaire accueil", "resources/views/home.blade.php", "42-64"),
            ("Formulaire detaille", "resources/views/posts/create.blade.php", "10-28"),
        ],
    },
    {
        "title": "6. Modification d une publication",
        "category": "Posts",
        "summary": "Seul l auteur du post peut ouvrir le formulaire puis mettre a jour le contenu. C est une bonne demonstration de controle d acces simple.",
        "related": "Associe a la securite de base : un utilisateur ne doit pas modifier le contenu d un autre.",
        "refs": [
            ("Controleur edit", "app/Http/Controllers/PostController.php", "49-55"),
            ("Controleur update", "app/Http/Controllers/PostController.php", "57-69"),
            ("Vue edit", "resources/views/posts/edit.blade.php", "10-28"),
            ("Bouton dans la carte", "resources/views/posts/_card.blade.php", "26-30"),
        ],
    },
    {
        "title": "7. Suppression d une publication",
        "category": "Posts",
        "summary": "La suppression est reservee au proprietaire du post. Comme les relations SQL utilisent `cascadeOnDelete`, les enregistrements dependants comme likes ou commentaires peuvent etre geres proprement par la base.",
        "related": "Fonction annexe utile a expliquer pour parler d integrite referentielle.",
        "refs": [
            ("Controleur destroy", "app/Http/Controllers/PostController.php", "72-79"),
            ("Bouton de suppression", "resources/views/posts/_card.blade.php", "30-36"),
        ],
    },
    {
        "title": "8. Fil d actualite base sur les abonnements",
        "category": "Social",
        "summary": "Le feed montre les publications des personnes suivies ainsi que celles de l utilisateur lui meme. On recupere d abord les identifiants suivis, puis on charge les posts correspondants.",
        "related": "Lie a la table `followers`, au modele User et a la page d accueil.",
        "refs": [
            ("Recuperation des ids suivis", "app/Http/Controllers/HomeController.php", "19-23"),
            ("Table followers", "database/migrations/2026_03_19_164551_create_followers_table.php", "11-25"),
            ("Relations User", "app/Models/User.php", "101-126"),
        ],
    },
    {
        "title": "9. Suivre un utilisateur",
        "category": "Social",
        "summary": "Le controleur ajoute une relation dans la table pivot `followers`. Il evite aussi qu un utilisateur se suive lui meme.",
        "related": "Associe aux suggestions, aux profils et aux statistiques d abonnes.",
        "refs": [
            ("Route", "routes/web.php", "37"),
            ("Controleur", "app/Http/Controllers/FollowController.php", "14-25"),
            ("Bouton suggestions", "resources/views/home.blade.php", "129-152"),
        ],
    },
    {
        "title": "10. Ne plus suivre un utilisateur",
        "category": "Social",
        "summary": "L action retire la relation dans la table pivot. C est le pendant logique du follow.",
        "related": "Fonction annexe du systeme d abonnements.",
        "refs": [
            ("Route", "routes/web.php", "38"),
            ("Controleur", "app/Http/Controllers/FollowController.php", "27-31"),
            ("Bouton profil", "resources/views/profiles/show.blade.php", "43-54"),
        ],
    },
    {
        "title": "11. Annuaire des membres",
        "category": "Profils",
        "summary": "La page membre affiche la liste des utilisateurs avec une recherche par nom, email ou localisation. C est simple mais tres typique d un exercice de CRUD et de filtre.",
        "related": "Fonction utile pour decouvrir des profils et lancer une conversation privee.",
        "refs": [
            ("Route", "routes/web.php", "19"),
            ("Controleur", "app/Http/Controllers/ProfileController.php", "10-28"),
            ("Vue", "resources/views/profiles/index.blade.php", "4-50"),
        ],
    },
    {
        "title": "12. Consultation d un profil public",
        "category": "Profils",
        "summary": "Chaque utilisateur a une page profil avec ses informations, ses statistiques et ses publications. C est une fonctionnalite centrale d un reseau social.",
        "related": "Elle se connecte aux followers, favoris publics, messagerie et posts.",
        "refs": [
            ("Route", "routes/web.php", "20"),
            ("Controleur", "app/Http/Controllers/ProfileController.php", "30-56"),
            ("Vue", "resources/views/profiles/show.blade.php", "4-75"),
        ],
    },
    {
        "title": "13. Listes des abonnes et des abonnements",
        "category": "Profils",
        "summary": "Le profil permet d ouvrir une liste des personnes qui suivent un compte et une autre liste des comptes suivis. C est utile pour expliquer les relations many-to-many.",
        "related": "Annexe directe du systeme de follow.",
        "refs": [
            ("Routes", "routes/web.php", "21-22"),
            ("Controleur followers", "app/Http/Controllers/ProfileController.php", "59-68"),
            ("Controleur following", "app/Http/Controllers/ProfileController.php", "71-80"),
            ("Vue liste", "resources/views/profiles/relations.blade.php", "4-33"),
        ],
    },
    {
        "title": "14. Modification des informations du profil",
        "category": "Profils",
        "summary": "Un utilisateur peut modifier son nom, son email, sa bio, sa localisation, son site web, sa date de naissance et eventuellement son mot de passe.",
        "related": "Cette fonctionnalite est souvent attendue dans un sujet de gestion de compte utilisateur.",
        "refs": [
            ("Routes", "routes/web.php", "44-45"),
            ("Controleur", "app/Http/Controllers/ProfileSettingsController.php", "12-58"),
            ("Vue formulaire", "resources/views/profiles/edit.blade.php", "13-68"),
            ("Colonnes SQL", "database/migrations/2026_06_29_120500_add_profile_fields_to_users_table.php", "11-16"),
        ],
    },
    {
        "title": "15. Photo de profil (ajout, remplacement, suppression)",
        "category": "Profils",
        "summary": "La photo est stockee dans le disque `public` de Laravel. Le controleur gere le cas ou l utilisateur remplace ou supprime son image actuelle.",
        "related": "Annexe directe des reglages profil et visible dans l interface.",
        "refs": [
            ("Validation et upload", "app/Http/Controllers/ProfileSettingsController.php", "23-46"),
            ("Suppression d avatar", "app/Http/Controllers/ProfileSettingsController.php", "35-37"),
            ("Helper URL", "app/Models/User.php", "133-139"),
            ("Composant avatar", "resources/views/users/_avatar.blade.php", "1-15"),
        ],
    },
    {
        "title": "16. Likes sur les publications",
        "category": "Interactions",
        "summary": "Le like est gere comme une table pivot `post_likes`. Cela montre bien comment relier utilisateurs et publications sans dupliquer de colonnes dans `posts`.",
        "related": "Lie au compteur de likes et aux statistiques du tableau de bord.",
        "refs": [
            ("Routes", "routes/web.php", "51-52"),
            ("Controleur", "app/Http/Controllers/LikeController.php", "9-20"),
            ("Modele User", "app/Models/User.php", "76-79"),
            ("Migration", "database/migrations/2026_06_29_120100_create_post_likes_table.php", "13-20"),
            ("Boutons vue", "resources/views/posts/_card.blade.php", "48-57"),
        ],
    },
    {
        "title": "17. Commentaires sur les publications",
        "category": "Interactions",
        "summary": "Les commentaires sont stockes dans une table separee. L auteur du commentaire, ou l auteur du post, peut supprimer le commentaire.",
        "related": "Fonction sociale classique, facile a expliquer avec une relation one-to-many.",
        "refs": [
            ("Routes", "routes/web.php", "57-58"),
            ("Controleur", "app/Http/Controllers/CommentController.php", "11-35"),
            ("Migration", "database/migrations/2026_06_29_120300_create_comments_table.php", "13-18"),
            ("Affichage et formulaire", "resources/views/posts/_card.blade.php", "71-110"),
        ],
    },
    {
        "title": "18. Ajout et retrait des favoris",
        "category": "Interactions",
        "summary": "Chaque utilisateur peut enregistrer des publications dans sa liste personnelle de favoris. La relation est geree dans la table pivot `favorite_posts`.",
        "related": "Lie aux favoris publics et a la limite de 50 elements.",
        "refs": [
            ("Routes", "routes/web.php", "41-42 et 54-55"),
            ("Controleur", "app/Http/Controllers/FavoriteController.php", "11-85"),
            ("Modele User", "app/Models/User.php", "81-84"),
            ("Migration", "database/migrations/2026_06_29_120200_create_favorite_posts_table.php", "13-20"),
            ("Boutons", "resources/views/posts/_card.blade.php", "59-67"),
        ],
    },
    {
        "title": "19. Limite de 50 favoris",
        "category": "Interactions",
        "summary": "Le controleur refuse l ajout d un nouveau favori si la liste contient deja 50 publications. C est un exemple tres clair de regle metier.",
        "related": "Fonction annexe du systeme de favoris.",
        "refs": [
            ("Controle de la limite", "app/Http/Controllers/FavoriteController.php", "58-64"),
            ("Test automatise", "tests/Feature/SocialFeaturesTest.php", "16-40"),
        ],
    },
    {
        "title": "20. Favoris publics ou prives",
        "category": "Interactions",
        "summary": "Un utilisateur choisit si sa liste de favoris reste privee ou devient visible publiquement. Cette information est stockee dans `users.favorite_posts_public`.",
        "related": "Lie a la page `Mes favoris` et a la consultation publique des favoris d un autre profil.",
        "refs": [
            ("Mise a jour de visibilite", "app/Http/Controllers/FavoriteController.php", "78-85"),
            ("Vue personnelle", "resources/views/favorites/index.blade.php", "5-29"),
            ("Vue publique", "resources/views/favorites/show.blade.php", "4-14"),
            ("Champ SQL", "database/migrations/2026_06_29_120000_add_social_fields_to_users_table.php", "11-14"),
        ],
    },
    {
        "title": "21. Explorer et rechercher des publications",
        "category": "Navigation",
        "summary": "La page Explorer permet de chercher un texte dans le contenu d un post ou dans le nom de son auteur. Elle est utile meme pour les visiteurs non connectes.",
        "related": "Elle se combine avec la moderation, car certains posts peuvent etre masques en mode visiteur.",
        "refs": [
            ("Route", "routes/web.php", "18"),
            ("Controleur", "app/Http/Controllers/ExploreController.php", "10-40"),
            ("Vue", "resources/views/explore.blade.php", "4-26"),
            ("Champ recherche global", "resources/views/layouts/app.blade.php", "145-150"),
        ],
    },
    {
        "title": "22. Signaler une publication",
        "category": "Moderation",
        "summary": "Un utilisateur peut envoyer un signalement avec une raison. Le code empeche d envoyer plusieurs signalements en attente sur le meme post par la meme personne.",
        "related": "Lie au panneau d administration et au masquage des posts pour les visiteurs.",
        "refs": [
            ("Route", "routes/web.php", "60"),
            ("Controleur", "app/Http/Controllers/ReportController.php", "10-31"),
            ("Migration", "database/migrations/2026_06_29_120400_create_post_reports_table.php", "13-21"),
            ("Formulaire vue", "resources/views/posts/_card.blade.php", "105-110"),
        ],
    },
    {
        "title": "23. Moderation admin des signalements",
        "category": "Moderation",
        "summary": "Un administrateur consulte la liste des signalements puis choisit de les accepter ou de les rejeter. Le premier utilisateur du projet peut devenir admin automatiquement.",
        "related": "Tres pratique a expliquer pour parler de role, autorisation et cycle de traitement.",
        "refs": [
            ("Routes", "routes/web.php", "62-63"),
            ("Controleur admin", "app/Http/Controllers/AdminReportController.php", "10-41"),
            ("Vue admin", "resources/views/admin/reports/index.blade.php", "4-58"),
            ("Premier admin", "app/Http/Controllers/Auth/RegisterController.php", "64-71"),
        ],
    },
    {
        "title": "24. Masquer les posts signales aux visiteurs",
        "category": "Moderation",
        "summary": "Quand un visiteur non connecte consulte Explorer ou des favoris publics, les publications ayant un signalement en attente ou accepte sont exclues.",
        "related": "Bonne illustration d une regle metier implemente a la fois dans le modele et dans les controleurs.",
        "refs": [
            ("Scope du modele", "app/Models/Post.php", "43-47"),
            ("Utilisation dans Explorer", "app/Http/Controllers/ExploreController.php", "25-27"),
            ("Utilisation dans favoris publics", "app/Http/Controllers/FavoriteController.php", "47-53"),
            ("Test automatise", "tests/Feature/SocialFeaturesTest.php", "42-80"),
        ],
    },
    {
        "title": "25. Notes privees",
        "category": "Productivite",
        "summary": "Le projet inclut aussi un mini module de notes privees. C est tres bien pour montrer un CRUD complet simple, meme si ce n est pas le coeur du reseau social.",
        "related": "Annexe utile pour montrer la maitrise du pattern CRUD.",
        "refs": [
            ("Routes resource", "routes/web.php", "29-31"),
            ("Controleur", "app/Http/Controllers/NoteController.php", "15-75"),
            ("Table notes", "database/migrations/2026_03_19_000000_create_notes_table.php", "12-17"),
            ("Bloc accueil", "resources/views/home.blade.php", "80-115"),
        ],
    },
    {
        "title": "26. Messagerie privee entre deux utilisateurs",
        "category": "Messagerie",
        "summary": "Deux utilisateurs peuvent ouvrir une conversation puis s envoyer des messages texte simples. Les messages sont stockes dans une table dediee `messages`.",
        "related": "Lie au profil public, a l annuaire des membres et au compteur de messages non lus.",
        "refs": [
            ("Routes", "routes/web.php", "47-49"),
            ("Controleur liste + conversation", "app/Http/Controllers/MessageController.php", "11-79"),
            ("Modele", "app/Models/Message.php", "8-32"),
            ("Migration", "database/migrations/2026_06_29_120600_create_messages_table.php", "12-18"),
            ("Vue liste", "resources/views/messages/index.blade.php", "4-36"),
            ("Vue conversation", "resources/views/messages/show.blade.php", "4-48"),
        ],
    },
    {
        "title": "27. Compteur de messages non lus",
        "category": "Messagerie",
        "summary": "Le modele User calcule le nombre de messages recus qui n ont pas encore de date `read_at`. Cette valeur est affichee dans la navigation et dans la liste des conversations.",
        "related": "Sous-fonctionnalite de la messagerie, interessante pour parler d etat d un message.",
        "refs": [
            ("Compteur dans User", "app/Models/User.php", "128-130"),
            ("Navigation", "resources/views/layouts/app.blade.php", "112-117 et 152-158"),
            ("Liste des conversations", "resources/views/messages/index.blade.php", "26-30"),
        ],
    },
    {
        "title": "28. Lecture automatique d une conversation",
        "category": "Messagerie",
        "summary": "Lorsqu on ouvre une conversation, les messages recus dans cette discussion passent en `read_at = now()`. C est simple et pedagogique pour expliquer l evolution d un etat.",
        "related": "Sous-fonctionnalite de la messagerie et du compteur non lu.",
        "refs": [
            ("Marquage comme lu", "app/Http/Controllers/MessageController.php", "48-51"),
            ("Affichage de l etat", "resources/views/messages/show.blade.php", "27-31"),
        ],
    },
]


def build_story():
    story = []

    story.append(Spacer(1, 1.2 * cm))
    story.append(p("Document technique SkyStorm", "DocTitle"))
    story.append(p("Guide debutant du projet Laravel realise pour un contexte BTS SIO", "DocSubTitle"))
    story.append(p("Date : 29/06/2026", "DocSubTitle"))
    story.append(Spacer(1, 0.7 * cm))
    story.append(
        p(
            "Ce document explique le projet comme si le lecteur debutait en programmation. "
            "La premiere partie presente l architecture generale du projet, les routes, la base de donnees "
            "et les dossiers importants. La seconde partie detaille les fonctionnalites une par une avec les "
            "fichiers et lignes utiles pour les retrouver rapidement dans le code source."
        )
    )
    story.append(Spacer(1, 0.5 * cm))
    story.append(
        p(
            "<b>Projet :</b> mini reseau social Laravel nomme <b>SkyStorm</b>, avec publications, abonnements, "
            "favoris, profils, moderation, notes et messagerie privee."
        )
    )
    story.append(PageBreak())

    story.append(p("1. Vue d ensemble du projet", "SectionTitle"))
    story.append(
        p(
            "SkyStorm est une application web ecrite avec le framework Laravel. Le but du projet est de proposer "
            "un reseau social simple, lisible et defendable a l oral : l utilisateur cree un compte, publie des messages, "
            "suit d autres comptes, ajoute des favoris, commente, signale un contenu et peut discuter en prive avec d autres membres."
        )
    )
    story.append(
        p(
            "La logique a volontairement ete gardee simple : beaucoup de fonctionnalites sont gerees avec des "
            "controleurs courts, des vues Blade lisibles et des tables SQL bien separees. C est un bon format pour un examen, "
            "parce qu on peut rapidement montrer le chemin complet d une fonctionnalite : route -> controleur -> modele -> vue -> base de donnees."
        )
    )
    for item in [
        "Base technique : Laravel 12, PHP 8.2, Blade, migrations Eloquent, SQLite dans le projet actuel.",
        "Partie front : pages Blade avec Bootstrap et quelques styles personalises dans le layout.",
        "Build front : Vite charge les assets CSS, Sass et JavaScript.",
        "Logique metier : centralisee dans app/Http/Controllers/ et les relations des modeles Eloquent.",
    ]:
        story.append(p(f"- {item}"))

    story.append(p("2. Rappel debutant : architecture MVC", "SectionTitle"))
    story.append(
        p(
            "<b>MVC</b> signifie <b>Model - View - Controller</b>. "
            "C est une facon d organiser le projet pour separer les responsabilites."
        )
    )
    mvc_table = Table(
        [
            ["Composant", "Role simple", "Exemples dans le projet"],
            ["Model", "Represente les donnees et les relations avec la base", "app/Models/User.php, Post.php, Message.php"],
            ["View", "Affiche les pages HTML a l utilisateur", "resources/views/home.blade.php, posts/_card.blade.php"],
            ["Controller", "Recoit la requete, applique les regles, choisit la vue", "HomeController.php, PostController.php, MessageController.php"],
            ["Route", "Associe une URL a un controleur", "routes/web.php"],
        ],
        colWidths=[3.0 * cm, 7.2 * cm, 6.0 * cm],
    )
    mvc_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#dbeafe")),
                ("FONTNAME", (0, 0), (-1, 0), "Arial-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Arial"),
                ("FONTSIZE", (0, 0), (-1, -1), 8.5),
                ("LEADING", (0, 0), (-1, -1), 11),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 5),
                ("RIGHTPADDING", (0, 0), (-1, -1), 5),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    story.append(mvc_table)
    story.append(Spacer(1, 0.3 * cm))
    story.append(
        p(
            "Exemple concret quand un utilisateur like un post : la route appelle <font name='Courier'>LikeController</font>, "
            "le controleur ajoute une relation dans la table <font name='Courier'>post_likes</font>, puis Laravel revient a la page precedente avec un message de succes."
        )
    )
    story.append(code_ref("Route du like", "routes/web.php", "51-52"))
    story.append(code_ref("Controleur du like", "app/Http/Controllers/LikeController.php", "9-20"))
    story.append(code_ref("Vue du bouton like", "resources/views/posts/_card.blade.php", "48-57"))

    story.append(p("3. Organisation des dossiers", "SectionTitle"))
    story.append(
        p(
            "Pour un debutant, le plus important est de savoir <i>ou chercher</i>. "
            "La structure ci-dessous montre les zones principales du projet."
        )
    )
    story.append(Preformatted(important_tree, styles["CodeLine"]))
    story.append(
        p(
            "Si tu veux comprendre une fonctionnalite pendant l oral, commence presque toujours par "
            "<font name='Courier'>routes/web.php</font>, puis ouvre le controleur cible, puis le modele eventuel, puis la vue Blade."
        )
    )

    story.append(p("4. Fichiers les plus importants", "SectionTitle"))
    for label, path, lines, desc in [
        ("Routes principales", "routes/web.php", "14-64", "Toutes les URL et les actions disponibles."),
        ("Modele utilisateur", "app/Models/User.php", "22-140", "Profil, relations followers, favoris, messages et helpers."),
        ("Modele post", "app/Models/Post.php", "12-48", "Relations du post et regle de visibilite pour les visiteurs."),
        ("Layout commun", "resources/views/layouts/app.blade.php", "81-181", "Navigation, topbar, messages flash et structure des pages."),
        ("Accueil", "app/Http/Controllers/HomeController.php", "15-49", "Construction du feed et des statistiques."),
        ("Carte d un post", "resources/views/posts/_card.blade.php", "7-112", "Actions likes, favoris, commentaires, signalement."),
    ]:
        story.append(p(f"<b>{label}</b> - {desc}"))
        story.append(code_ref("Emplacement", path, lines))

    story.append(PageBreak())
    story.append(p("5. Les routes du projet", "SectionTitle"))
    story.append(
        p(
            "Les routes jouent le role d annuaire de l application. C est elles qui disent : "
            "quand un navigateur demande une URL, quel controleur doit reagir ?"
        )
    )
    route_table = Table(route_rows, colWidths=[3.2 * cm, 5.4 * cm, 8.0 * cm])
    route_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#dbeafe")),
                ("FONTNAME", (0, 0), (-1, 0), "Arial-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Arial"),
                ("FONTSIZE", (0, 0), (-1, -1), 8.5),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 4),
                ("RIGHTPADDING", (0, 0), (-1, -1), 4),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    story.append(route_table)
    story.append(Spacer(1, 0.2 * cm))
    story.append(code_ref("Fichier central des routes", "routes/web.php", "18-64"))

    story.append(p("6. La base de donnees", "SectionTitle"))
    story.append(
        p(
            "Laravel construit la base a partir des <b>migrations</b>. Une migration est un fichier PHP qui decrit "
            "une table SQL : ses colonnes, ses cles et ses contraintes. C est pratique car le schema de la base reste versionne avec le code."
        )
    )
    db_table = Table(database_rows, colWidths=[2.7 * cm, 5.9 * cm, 8.0 * cm])
    db_table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#dbeafe")),
                ("FONTNAME", (0, 0), (-1, 0), "Arial-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Arial"),
                ("FONTSIZE", (0, 0), (-1, -1), 7.8),
                ("LEADING", (0, 0), (-1, -1), 10),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#cbd5e1")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 4),
                ("RIGHTPADDING", (0, 0), (-1, -1), 4),
                ("TOPPADDING", (0, 0), (-1, -1), 4),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
            ]
        )
    )
    story.append(db_table)
    story.append(Spacer(1, 0.2 * cm))
    for label, path, lines in [
        ("Users de base", "database/migrations/0001_01_01_000000_create_users_table.php", "14-37"),
        ("Posts", "database/migrations/2026_02_12_185401_create_posts_table.php", "15-20"),
        ("Followers", "database/migrations/2026_03_19_164551_create_followers_table.php", "11-25"),
        ("Champs profil", "database/migrations/2026_06_29_120500_add_profile_fields_to_users_table.php", "11-16"),
        ("Messages", "database/migrations/2026_06_29_120600_create_messages_table.php", "12-18"),
    ]:
        story.append(code_ref(label, path, lines))

    story.append(PageBreak())
    story.append(p("7. Cycle d une requete : exemple simple", "SectionTitle"))
    story.append(
        p(
            "Prenons un exemple concret : <b>ajouter une publication aux favoris</b>. "
            "Ce scenario aide a comprendre comment tout s enchaine dans Laravel."
        )
    )
    for step in [
        "1. L utilisateur clique sur le bouton dans la vue Blade du post.",
        "2. Le formulaire envoie une requete POST vers la route `posts.favorites.store`.",
        "3. La route appelle `FavoriteController@store`.",
        "4. Le controleur applique la regle metier : maximum 50 favoris.",
        "5. Le modele User ajoute la relation dans la table pivot `favorite_posts`.",
        "6. Laravel redirige avec un message de succes visible dans le layout.",
    ]:
        story.append(p(step))
    for label, path, lines in [
        ("Bouton dans la carte du post", "resources/views/posts/_card.blade.php", "59-67"),
        ("Route POST", "routes/web.php", "54-55"),
        ("Controleur FavoriteController", "app/Http/Controllers/FavoriteController.php", "58-68"),
        ("Table pivot favorite_posts", "database/migrations/2026_06_29_120200_create_favorite_posts_table.php", "13-20"),
        ("Message flash", "resources/views/layouts/app.blade.php", "163-169"),
    ]:
        story.append(code_ref(label, path, lines))

    story.append(PageBreak())
    story.append(p("8. Fiches fonctionnalites", "SectionTitle"))
    story.append(
        p(
            "Les fiches suivantes sont concues pour la revision. Pour chaque fonctionnalite : "
            "tu trouves son but, sa logique simple et les fichiers utiles a ouvrir dans l ordre."
        )
    )

    current_category = None
    for feature in features:
        if feature["category"] != current_category:
            current_category = feature["category"]
            story.append(p(current_category, "SubSectionTitle"))

        story.append(p(feature["title"], "FeatureTitle"))
        story.append(p(feature["summary"]))
        story.append(p(f"<b>Fonctionnalites associees :</b> {feature['related']}"))
        story.append(p("<b>Ou regarder dans le code :</b>", "BodySmall"))
        for label, path, lines in feature["refs"]:
            story.append(code_ref(label, path, lines))
        story.append(Spacer(1, 0.1 * cm))

    story.append(PageBreak())
    story.append(p("9. Tests et verification", "SectionTitle"))
    story.append(
        p(
            "Un petit fichier de tests a ete ajoute pour verifier quelques regles metier importantes. "
            "Dans un oral, cela montre que le projet n est pas seulement code, mais aussi verifie."
        )
    )
    story.append(code_ref("Tests fonctionnels", "tests/Feature/SocialFeaturesTest.php", "12-132"))
    for item in [
        "La limite de 50 favoris est testee.",
        "Le masquage des posts signales pour les visiteurs est teste.",
        "Le traitement d un signalement par un admin est teste.",
        "L envoi d un message prive est teste.",
    ]:
        story.append(p(f"- {item}"))

    story.append(p("10. Commandes utiles pour faire tourner le projet", "SectionTitle"))
    story.append(
        p(
            "Pour un examinateur, il est souvent utile de montrer que tu sais lancer le projet et expliquer les commandes de base."
        )
    )
    commands = [
        "php artisan migrate",
        "php artisan storage:link",
        "npm run app",
        "php artisan test",
    ]
    for command in commands:
        story.append(Paragraph(f"<font name='Courier'>{escape(command)}</font>", styles["Body"]))

    story.append(
        p(
            "<b>Pourquoi `storage:link` ?</b> Parce que la photo de profil utilise le disque public de Laravel. "
            "Le lien symbolique permet au navigateur d acceder aux fichiers stockes dans `storage/app/public`."
        )
    )

    story.append(p("11. Conseils pour presenter le projet a l oral", "SectionTitle"))
    for advice in [
        "Commence par l architecture MVC avant de parler des details.",
        "Montre une fonctionnalite de bout en bout : route, controleur, modele, vue, table SQL.",
        "Utilise la messagerie, les favoris ou le signalement comme exemples, car le chemin technique y est tres clair.",
        "Si on te demande une regle metier, cite la limite de 50 favoris ou le filtrage des posts signales pour les visiteurs.",
        "Si on te demande la securite, cite l authentification, les controles d auteur sur les posts et la verification du role admin.",
    ]:
        story.append(p(f"- {advice}"))

    story.append(Spacer(1, 0.4 * cm))
    story.append(
        p(
            "Fin du document. Ce support est volontairement detaille pour pouvoir servir a la fois de fiche de revision "
            "et de guide de navigation dans le code source."
        )
    )

    return story


def main() -> None:
    TMP.mkdir(parents=True, exist_ok=True)
    OUTPUT.parent.mkdir(parents=True, exist_ok=True)

    doc = SimpleDocTemplate(
        str(OUTPUT),
        pagesize=A4,
        leftMargin=1.7 * cm,
        rightMargin=1.7 * cm,
        topMargin=1.8 * cm,
        bottomMargin=1.4 * cm,
        title="SkyStorm - Document technique",
        author="OpenAI Codex",
    )

    doc.build(build_story(), onFirstPage=add_header_footer, onLaterPages=add_header_footer)
    print(OUTPUT)


if __name__ == "__main__":
    main()
