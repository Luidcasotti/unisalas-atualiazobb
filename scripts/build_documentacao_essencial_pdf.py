from pathlib import Path
import html

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, Preformatted


ROOT = Path(__file__).resolve().parents[1]
MD_PATH = ROOT / "docs" / "DOCUMENTACAO_ESSENCIAL_UNISALAS.md"
PDF_PATH = ROOT / "docs" / "DOCUMENTACAO_ESSENCIAL_UNISALAS.pdf"


def styles():
    base = getSampleStyleSheet()
    return {
        "title": ParagraphStyle("Title", parent=base["Title"], fontName="Helvetica-Bold", fontSize=20, leading=24, alignment=TA_CENTER, textColor=colors.HexColor("#173F5F"), spaceAfter=14),
        "h1": ParagraphStyle("H1", parent=base["Heading1"], fontName="Helvetica-Bold", fontSize=14, leading=17, textColor=colors.HexColor("#1F4E79"), spaceBefore=10, spaceAfter=6),
        "h2": ParagraphStyle("H2", parent=base["Heading2"], fontName="Helvetica-Bold", fontSize=11.5, leading=14, textColor=colors.HexColor("#244766"), spaceBefore=6, spaceAfter=4),
        "body": ParagraphStyle("Body", parent=base["BodyText"], fontName="Helvetica", fontSize=9, leading=12.2, alignment=TA_LEFT, spaceAfter=4),
        "bullet": ParagraphStyle("Bullet", parent=base["BodyText"], fontName="Helvetica", fontSize=9, leading=12, leftIndent=12, firstLineIndent=-7, spaceAfter=2.5),
        "code": ParagraphStyle("Code", parent=base["Code"], fontName="Courier", fontSize=7.2, leading=9.2, spaceBefore=3, spaceAfter=5),
        "cell": ParagraphStyle("Cell", parent=base["BodyText"], fontName="Helvetica", fontSize=7.5, leading=9.5),
        "head": ParagraphStyle("Head", parent=base["BodyText"], fontName="Helvetica-Bold", fontSize=7.5, leading=9.5, textColor=colors.HexColor("#173F5F")),
    }


def inline(text):
    text = html.escape(text)
    parts = text.split("**")
    for i in range(1, len(parts), 2):
        parts[i] = f"<b>{parts[i]}</b>"
    text = "".join(parts)
    parts = text.split("`")
    for i in range(1, len(parts), 2):
        parts[i] = f"<font name='Courier'>{parts[i]}</font>"
    return "".join(parts)


def add_table(story, rows, st):
    rows = [r for r in rows if not all(set(c.strip()) <= {"-", ":"} for c in r)]
    if not rows:
        return
    cols = max(len(r) for r in rows)
    width = 16.0 * cm / cols
    data = []
    for index, row in enumerate(rows):
        style = st["head"] if index == 0 else st["cell"]
        row = row + [""] * (cols - len(row))
        data.append([Paragraph(inline(c.strip()), style) for c in row])
    table = Table(data, colWidths=[width] * cols, repeatRows=1)
    table.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#E8F0F7")),
        ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#9AA9B5")),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("LEFTPADDING", (0, 0), (-1, -1), 4),
        ("RIGHTPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 3),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
    ]))
    story.append(table)
    story.append(Spacer(1, 5))


def parse():
    st = styles()
    story = []
    in_code = False
    code = []
    table = []

    def flush_code():
        nonlocal code
        if code:
            story.append(Preformatted("\n".join(code), st["code"], maxLineLength=105))
            code = []

    def flush_table():
        nonlocal table
        if table:
            add_table(story, table, st)
            table = []

    for raw in MD_PATH.read_text(encoding="utf-8").splitlines():
        line = raw.rstrip()
        if line.startswith("```"):
            if in_code:
                flush_code()
            else:
                flush_table()
            in_code = not in_code
            continue
        if in_code:
            code.append(line)
            continue
        if line.startswith("|") and line.endswith("|"):
            table.append(line.strip("|").split("|"))
            continue
        flush_table()
        if not line.strip():
            story.append(Spacer(1, 3))
        elif line.startswith("# "):
            story.append(Paragraph(inline(line[2:]), st["title"]))
        elif line.startswith("## "):
            story.append(Paragraph(inline(line[3:]), st["h1"]))
        elif line.startswith("### "):
            story.append(Paragraph(inline(line[4:]), st["h2"]))
        elif line.startswith("- "):
            story.append(Paragraph("• " + inline(line[2:]), st["bullet"]))
        else:
            story.append(Paragraph(inline(line), st["body"]))
    flush_table()
    flush_code()
    return story


def main():
    doc = SimpleDocTemplate(str(PDF_PATH), pagesize=A4, leftMargin=2.2 * cm, rightMargin=2.0 * cm, topMargin=2.0 * cm, bottomMargin=1.8 * cm)
    doc.build(parse())
    print(PDF_PATH)


if __name__ == "__main__":
    main()
