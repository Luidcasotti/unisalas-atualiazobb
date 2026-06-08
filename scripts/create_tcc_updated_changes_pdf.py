from pathlib import Path

import fitz
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_JUSTIFY, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    SimpleDocTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
)


ROOT = Path(__file__).resolve().parents[1]
ORIGINAL = Path(r"C:\Users\Luid Casotti\Downloads\tcc_luid.pdf")
OUT_DIR = ROOT / "docs"
ADDENDUM = OUT_DIR / "tcc_luid_mudancas_anexo.pdf"
FINAL = OUT_DIR / "tcc_luid_atualizado_mudancas.pdf"


def p(text: str, style: ParagraphStyle) -> Paragraph:
    return Paragraph(text, style)


def build_addendum() -> None:
    OUT_DIR.mkdir(exist_ok=True)

    styles = getSampleStyleSheet()
    title = ParagraphStyle(
        "TitleCustom",
        parent=styles["Title"],
        fontName="Helvetica-Bold",
        fontSize=18,
        leading=22,
        alignment=TA_CENTER,
        textColor=colors.HexColor("#1F4E79"),
        spaceAfter=14,
    )
    h1 = ParagraphStyle(
        "Heading1Custom",
        parent=styles["Heading1"],
        fontName="Helvetica-Bold",
        fontSize=13,
        leading=16,
        textColor=colors.HexColor("#1F4E79"),
        spaceBefore=10,
        spaceAfter=6,
    )
    body = ParagraphStyle(
        "BodyCustom",
        parent=styles["BodyText"],
        fontName="Helvetica",
        fontSize=10.5,
        leading=15,
        alignment=TA_JUSTIFY,
        spaceAfter=7,
    )
    bullet = ParagraphStyle(
        "BulletCustom",
        parent=body,
        leftIndent=14,
        firstLineIndent=-8,
        alignment=TA_LEFT,
    )
    small = ParagraphStyle(
        "SmallCustom",
        parent=body,
        fontSize=9,
        leading=12,
        alignment=TA_LEFT,
    )

    doc = SimpleDocTemplate(
        str(ADDENDUM),
        pagesize=A4,
        rightMargin=2.5 * cm,
        leftMargin=2.5 * cm,
        topMargin=2.2 * cm,
        bottomMargin=2.2 * cm,
        title="Atualizacao do TCC - UniSalas",
    )

    story = [
        p("ATUALIZAÇÃO DO TCC - MUDANÇAS IMPLEMENTADAS NO UNISALAS", title),
        p(
            "Este anexo complementa o Trabalho de Conclusão de Curso com as mudanças "
            "implementadas na versão atual do sistema UniSalas. O objetivo é registrar "
            "apenas as novas funcionalidades, ajustes técnicos e melhorias realizadas, "
            "sem alterar a estrutura original do documento.",
            body,
        ),
        p("1. Atualização da implementação", h1),
        p(
            "O sistema UniSalas foi ampliado para deixar de ser apenas uma proposta de "
            "reserva de salas e passar a representar uma aplicação web funcional, com "
            "fluxos separados para administrador e professor. A implementação atual usa "
            "Laravel 12, PHP 8.2, Blade, Bootstrap, MySQL/MariaDB, Composer, Node.js, "
            "NPM e Vite, mantendo a organização MVC típica do framework Laravel.",
            body,
        ),
        p(
            "Também foi atualizada a documentação de instalação do projeto, incluindo "
            "instruções para criação do banco reserva_salas, execução das migrations, "
            "seed dos usuários iniciais e credenciais de teste para acesso ao sistema.",
            body,
        ),
        p("2. Novas funcionalidades administrativas", h1),
    ]

    admin_items = [
        "Criação de dashboard administrativo com indicadores gerais, avisos e mensagens.",
        "Gerenciamento de usuários com cadastro, edição, exclusão e separação por perfil.",
        "Gerenciamento de blocos e salas, incluindo cor do bloco e observações da sala.",
        "Controle de manutenção de blocos e salas, com prazo definido ou indeterminado.",
        "Cancelamento automático de reservas futuras afetadas por manutenção.",
        "Fila administrativa para aprovar, recusar ou cancelar reservas.",
        "Aprovação e recusa em lote de reservas recorrentes.",
        "Histórico administrativo filtrável por data, bloco, sala e período.",
        "Publicação e remoção de avisos institucionais.",
        "Troca de mensagens diretas entre administradores e professores.",
    ]
    story.extend(p(f"• {item}", bullet) for item in admin_items)

    story += [
        p("3. Novas funcionalidades para professores", h1),
    ]
    professor_items = [
        "Painel do professor com resumo de reservas, avisos e lembretes.",
        "Solicitação de reserva simples informando bloco, sala, data e período.",
        "Solicitação de reserva recorrente semanal por até três meses.",
        "Verificação de disponibilidade antes do registro da solicitação.",
        "Acompanhamento de reservas em tela própria de minhas reservas.",
        "Consulta de histórico de solicitações anteriores.",
        "Desistência de uma reserva específica vinculada ao professor.",
        "Visualização de comentários administrativos nas respostas das reservas.",
        "Envio e recebimento de mensagens diretas com a administração.",
        "Notificações visuais para mensagens não lidas e reservas respondidas.",
    ]
    story.extend(p(f"• {item}", bullet) for item in professor_items)

    story += [
        p("4. Regras de negócio acrescentadas", h1),
        p(
            "A versão atual inclui regras de negócio para tornar o processo de reserva "
            "mais controlado. O sistema verifica conflitos considerando sala, data e "
            "período, além de bloquear ou tratar solicitações quando a sala ou o bloco "
            "estão em manutenção. As reservas passam a possuir status como pendente, "
            "em análise, aprovada, rejeitada e cancelada.",
            body,
        ),
        p(
            "Nas reservas recorrentes, as datas geradas ficam vinculadas por um grupo "
            "de recorrência, permitindo que o administrador aprove ou recuse várias "
            "ocorrências de uma só vez. Ao aprovar uma reserva, o sistema também pode "
            "cancelar solicitações concorrentes para evitar duplicidade no mesmo espaço "
            "e período.",
            body,
        ),
        p("5. Atualização do banco de dados", h1),
    ]

    table_data = [
        ["Entidade", "Mudança registrada"],
        ["Aviso", "Nova entidade para comunicados exibidos nos painéis."],
        ["MensagemDireta", "Nova entidade para comunicação interna entre usuários."],
        ["Reserva", "Inclusão de observações, recorrência, grupo de recorrência e comentários."],
        ["Bloco", "Inclusão de cor visual e campos de manutenção."],
        ["Sala", "Inclusão de observações e campos de manutenção."],
        ["NotificacaoVisualizada", "Controle de visualização de notificações de reservas respondidas."],
    ]
    table = Table(table_data, colWidths=[4.2 * cm, 10.8 * cm])
    table.setStyle(
        TableStyle(
            [
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#D9EAF7")),
                ("TEXTCOLOR", (0, 0), (-1, 0), colors.HexColor("#1F4E79")),
                ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Helvetica"),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("LEADING", (0, 0), (-1, -1), 12),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#9EADBA")),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 7),
                ("RIGHTPADDING", (0, 0), (-1, -1), 7),
                ("TOPPADDING", (0, 0), (-1, -1), 6),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
            ]
        )
    )
    story.extend([table, Spacer(1, 10)])

    story += [
        p("6. Atualização dos resultados obtidos", h1),
        p(
            "Com as alterações, o resultado do projeto passa a ser um sistema operacional "
            "para reserva e gerenciamento de salas acadêmicas. A solução centraliza "
            "cadastros, solicitações, análise administrativa, histórico, comunicação, "
            "avisos e manutenção, reduzindo a dependência de controles manuais e "
            "aumentando a rastreabilidade das decisões.",
            body,
        ),
        p(
            "Como oportunidades futuras, permanecem a criação de relatórios gerenciais "
            "mais completos, exportação de dados, ampliação dos testes automatizados, "
            "integração com sistemas acadêmicos externos e divisão do controller "
            "principal em controllers menores por domínio funcional.",
            body,
        ),
        p("7. Texto sugerido para complementar a conclusão", h1),
        p(
            "A versão atual do UniSalas demonstra a viabilidade técnica da proposta ao "
            "entregar uma aplicação web funcional para professores e administradores. "
            "Além da solicitação de reservas, o sistema passou a contemplar recorrência, "
            "controle de manutenção, histórico, avisos, mensagens e notificações visuais. "
            "Esses recursos tornam o processo mais organizado, transparente e aderente "
            "às necessidades reais de gestão de salas acadêmicas.",
            body,
        ),
    ]

    doc.build(story)


def merge_pdf() -> None:
    final = fitz.open()
    with fitz.open(ORIGINAL) as original:
        final.insert_pdf(original)
    with fitz.open(ADDENDUM) as addendum:
        final.insert_pdf(addendum)
    final.save(FINAL)
    final.close()


if __name__ == "__main__":
    if not ORIGINAL.exists():
        raise FileNotFoundError(f"PDF original nao encontrado: {ORIGINAL}")
    build_addendum()
    merge_pdf()
    print(FINAL)
