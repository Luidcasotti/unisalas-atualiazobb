from pathlib import Path
import html

from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    SimpleDocTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
    PageBreak,
    Preformatted,
)


ROOT = Path(__file__).resolve().parents[1]
MD_PATH = ROOT / "docs" / "DOCUMENTACAO_COMPLETA_UNISALAS.md"
PDF_PATH = ROOT / "docs" / "DOCUMENTACAO_COMPLETA_UNISALAS.pdf"


def make_styles():
    base = getSampleStyleSheet()
    return {
        "title": ParagraphStyle(
            "DocTitle",
            parent=base["Title"],
            fontName="Helvetica-Bold",
            fontSize=20,
            leading=24,
            alignment=TA_CENTER,
            textColor=colors.HexColor("#163B5C"),
            spaceAfter=14,
        ),
        "h1": ParagraphStyle(
            "H1",
            parent=base["Heading1"],
            fontName="Helvetica-Bold",
            fontSize=14,
            leading=17,
            textColor=colors.HexColor("#1F4E79"),
            spaceBefore=10,
            spaceAfter=6,
        ),
        "h2": ParagraphStyle(
            "H2",
            parent=base["Heading2"],
            fontName="Helvetica-Bold",
            fontSize=12,
            leading=15,
            textColor=colors.HexColor("#244766"),
            spaceBefore=8,
            spaceAfter=5,
        ),
        "h3": ParagraphStyle(
            "H3",
            parent=base["Heading3"],
            fontName="Helvetica-Bold",
            fontSize=10.5,
            leading=13,
            textColor=colors.HexColor("#263B4D"),
            spaceBefore=6,
            spaceAfter=4,
        ),
        "body": ParagraphStyle(
            "Body",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=9,
            leading=12.2,
            alignment=TA_LEFT,
            spaceAfter=4,
        ),
        "bullet": ParagraphStyle(
            "Bullet",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=9,
            leading=12,
            leftIndent=12,
            firstLineIndent=-7,
            spaceAfter=2.5,
        ),
        "code": ParagraphStyle(
            "Code",
            parent=base["Code"],
            fontName="Courier",
            fontSize=7,
            leading=9,
            leftIndent=4,
            rightIndent=4,
            spaceBefore=3,
            spaceAfter=5,
        ),
        "cell": ParagraphStyle(
            "Cell",
            parent=base["BodyText"],
            fontName="Helvetica",
            fontSize=7,
            leading=9,
            alignment=TA_LEFT,
        ),
        "cell_head": ParagraphStyle(
            "CellHead",
            parent=base["BodyText"],
            fontName="Helvetica-Bold",
            fontSize=7,
            leading=9,
            alignment=TA_LEFT,
            textColor=colors.HexColor("#163B5C"),
        ),
    }


def inline(text: str) -> str:
    text = html.escape(text)
    bold_parts = text.split("**")
    for i in range(1, len(bold_parts), 2):
        bold_parts[i] = f"<b>{bold_parts[i]}</b>"
    text = "".join(bold_parts)
    parts = text.split("`")
    for i in range(1, len(parts), 2):
        parts[i] = f"<font name='Courier'>{parts[i]}</font>"
    return "".join(parts)


def add_table(story, rows, styles):
    if not rows:
        return

    col_count = max(len(r) for r in rows)
    widths = [16.0 * cm / col_count] * col_count
    rendered = []
    for row_index, row in enumerate(rows):
        style = styles["cell_head"] if row_index == 0 else styles["cell"]
        padded = row + [""] * (col_count - len(row))
        rendered.append([Paragraph(inline(cell.strip()), style) for cell in padded])

    table = Table(rendered, colWidths=widths, repeatRows=1)
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#E8F0F7")),
                ("GRID", (0, 0), (-1, -1), 0.35, colors.HexColor("#9AA9B5")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 4),
                ("RIGHTPADDING", (0, 0), (-1, -1), 4),
                ("TOPPADDING", (0, 0), (-1, -1), 3),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 3),
            ]
        )
    )
    story.append(table)
    story.append(Spacer(1, 5))


def parse_markdown():
    styles = make_styles()
    lines = MD_PATH.read_text(encoding="utf-8").splitlines()
    story = []
    code_lines = []
    in_code = False
    table_rows = []

    def flush_code():
        nonlocal code_lines
        if code_lines:
            story.append(Preformatted("\n".join(code_lines), styles["code"], maxLineLength=105))
            code_lines = []

    def flush_table():
        nonlocal table_rows
        if table_rows:
            rows = [r for r in table_rows if not all(set(c.strip()) <= {"-", ":"} for c in r)]
            add_table(story, rows, styles)
            table_rows = []

    for raw in lines:
        line = raw.rstrip()

        if line.startswith("```"):
            if in_code:
                flush_code()
                in_code = False
            else:
                flush_table()
                in_code = True
            continue

        if in_code:
            code_lines.append(line)
            continue

        if line.startswith("|") and line.endswith("|"):
            cells = line.strip("|").split("|")
            table_rows.append(cells)
            continue

        flush_table()

        if not line.strip():
            story.append(Spacer(1, 3))
            continue

        if line.startswith("# "):
            story.append(Paragraph(inline(line[2:]), styles["title"]))
            continue
        if line.startswith("## "):
            story.append(Paragraph(inline(line[3:]), styles["h1"]))
            continue
        if line.startswith("### "):
            story.append(Paragraph(inline(line[4:]), styles["h2"]))
            continue
        if line.startswith("#### "):
            story.append(Paragraph(inline(line[5:]), styles["h3"]))
            continue
        if line.startswith("- "):
            story.append(Paragraph("• " + inline(line[2:]), styles["bullet"]))
            continue
        if line.startswith("+ "):
            story.append(Paragraph("• " + inline(line[2:]), styles["bullet"]))
            continue
        if line.strip() == "---":
            story.append(PageBreak())
            continue

        story.append(Paragraph(inline(line), styles["body"]))

    flush_table()
    flush_code()
    return story


def build_pdf():
    doc = SimpleDocTemplate(
        str(PDF_PATH),
        pagesize=A4,
        leftMargin=2.2 * cm,
        rightMargin=2.0 * cm,
        topMargin=2.0 * cm,
        bottomMargin=1.8 * cm,
        title="Documentacao Completa UniSalas",
        author="Luid Casotti",
    )
    doc.build(parse_markdown())
    print(PDF_PATH)


if __name__ == "__main__":
    build_pdf()
