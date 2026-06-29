from pathlib import Path

import pypdfium2 as pdfium
from PIL import Image, ImageOps, ImageDraw


ROOT = Path("/Users/nkombelasseninathanmichel/Documents/GitHub/SkyStorm-BTS26")
PDF_PATH = ROOT / "output/pdf/skystorm-technical-document.pdf"
OUT_DIR = ROOT / "tmp/pdfs/rendered"
CONTACT_SHEET = ROOT / "tmp/pdfs/skystorm-technical-document-contact-sheet.png"


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)

    pdf = pdfium.PdfDocument(str(PDF_PATH))
    thumbs = []

    for index in range(len(pdf)):
        page = pdf[index]
        bitmap = page.render(scale=2.0)
        image = bitmap.to_pil()
        image_path = OUT_DIR / f"page-{index + 1:02d}.png"
        image.save(image_path)

        thumb = image.copy()
        thumb.thumbnail((260, 360))
        thumb = ImageOps.expand(thumb, border=2, fill="#d1d5db")
        thumbs.append((index + 1, thumb))

    cols = 2
    thumb_w = max(img.width for _, img in thumbs)
    thumb_h = max(img.height for _, img in thumbs)
    rows = (len(thumbs) + cols - 1) // cols
    canvas = Image.new("RGB", (cols * (thumb_w + 20) + 20, rows * (thumb_h + 40) + 20), "white")
    draw = ImageDraw.Draw(canvas)

    for idx, (page_no, thumb) in enumerate(thumbs):
        row = idx // cols
        col = idx % cols
        x = 20 + col * (thumb_w + 20)
        y = 20 + row * (thumb_h + 40)
        canvas.paste(thumb, (x, y))
        draw.text((x, y + thumb.height + 8), f"Page {page_no}", fill="#111827")

    canvas.save(CONTACT_SHEET)
    print(CONTACT_SHEET)


if __name__ == "__main__":
    main()
