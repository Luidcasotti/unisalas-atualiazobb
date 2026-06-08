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
    PageBreak,
)


ROOT = Path(__file__).resolve().parents[1]
OUT_DIR = ROOT / "docs"
PDF_PATH = OUT_DIR / "tcc_luid_corrigido.pdf"


def style_sheet():
    styles = getSampleStyleSheet()
    styles.add(
        ParagraphStyle(
            "Cover",
            parent=styles["Normal"],
            fontName="Times-Roman",
            fontSize=12,
            leading=18,
            alignment=TA_CENTER,
        )
    )
    styles.add(
        ParagraphStyle(
            "TitleTCC",
            parent=styles["Normal"],
            fontName="Times-Bold",
            fontSize=12,
            leading=18,
            alignment=TA_CENTER,
        )
    )
    styles.add(
        ParagraphStyle(
            "Chapter",
            parent=styles["Heading1"],
            fontName="Times-Bold",
            fontSize=12,
            leading=16,
            alignment=TA_LEFT,
            spaceBefore=10,
            spaceAfter=8,
        )
    )
    styles.add(
        ParagraphStyle(
            "Section",
            parent=styles["Heading2"],
            fontName="Times-Bold",
            fontSize=12,
            leading=15,
            alignment=TA_LEFT,
            spaceBefore=8,
            spaceAfter=6,
        )
    )
    styles.add(
        ParagraphStyle(
            "BodyTCC",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=12,
            leading=18,
            firstLineIndent=1.25 * cm,
            alignment=TA_JUSTIFY,
            spaceAfter=6,
        )
    )
    styles.add(
        ParagraphStyle(
            "QuoteTCC",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=10,
            leading=14,
            leftIndent=4 * cm,
            alignment=TA_JUSTIFY,
            spaceAfter=8,
        )
    )
    styles.add(
        ParagraphStyle(
            "RefTCC",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=11,
            leading=14,
            alignment=TA_LEFT,
            spaceAfter=8,
        )
    )
    styles.add(
        ParagraphStyle(
            "BulletTCC",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=12,
            leading=17,
            leftIndent=0.8 * cm,
            firstLineIndent=-0.35 * cm,
            alignment=TA_LEFT,
            spaceAfter=4,
        )
    )
    styles.add(
        ParagraphStyle(
            "Sumario",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=12,
            leading=18,
            alignment=TA_LEFT,
            spaceAfter=3,
        )
    )
    styles.add(
        ParagraphStyle(
            "Caption",
            parent=styles["BodyText"],
            fontName="Times-Roman",
            fontSize=11,
            leading=14,
            alignment=TA_LEFT,
            spaceAfter=6,
        )
    )
    return styles


def para(text, style):
    return Paragraph(text, style)


def add_center(story, text, styles, bold=False, space=0):
    story.append(para(text, styles["TitleTCC" if bold else "Cover"]))
    if space:
        story.append(Spacer(1, space))


def add_paragraphs(story, styles, paragraphs):
    for text in paragraphs:
        story.append(para(text, styles["BodyTCC"]))


def add_table(story, styles):
    data = [
        ["PROBLEMA", "SOLUÇÃO IMPLEMENTADA"],
        [
            "Falta de controle centralizado sobre o uso das salas de aula.",
            "Centralização de usuários, blocos, salas, reservas, avisos e mensagens em banco de dados.",
        ],
        [
            "Conflitos de horário entre solicitações.",
            "Verificação de conflito por sala, data e período antes da definição do status da reserva.",
        ],
        [
            "Indisponibilidade por manutenção não registrada.",
            "Controle de manutenção de bloco ou sala, com cancelamento de reservas futuras afetadas.",
        ],
        [
            "Solicitações recorrentes feitas manualmente.",
            "Geração de reservas semanais por até três meses, agrupadas por recorrência.",
        ],
        [
            "Demora na análise administrativa.",
            "Fila de aprovação com decisão individual e aprovação ou recusa em lote para recorrências.",
        ],
        [
            "Falta de comunicação entre professores e administração.",
            "Módulos de avisos institucionais, mensagens diretas e notificações visuais.",
        ],
        [
            "Dificuldade de acompanhamento.",
            "Telas de minhas reservas, histórico do professor e histórico administrativo filtrável.",
        ],
    ]
    table = Table(data, colWidths=[7.1 * cm, 7.1 * cm], repeatRows=1)
    table.setStyle(
        TableStyle(
            [
                ("FONTNAME", (0, 0), (-1, 0), "Times-Bold"),
                ("FONTNAME", (0, 1), (-1, -1), "Times-Roman"),
                ("FONTSIZE", (0, 0), (-1, -1), 9),
                ("LEADING", (0, 0), (-1, -1), 12),
                ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#E9EEF4")),
                ("GRID", (0, 0), (-1, -1), 0.5, colors.black),
                ("VALIGN", (0, 0), (-1, -1), "TOP"),
                ("LEFTPADDING", (0, 0), (-1, -1), 5),
                ("RIGHTPADDING", (0, 0), (-1, -1), 5),
                ("TOPPADDING", (0, 0), (-1, -1), 5),
                ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
            ]
        )
    )
    story.append(table)
    story.append(Spacer(1, 8))


REFERENCES = [
    "CASSARRO, Antonio C. Sistemas de informações para tomadas de decisões. 4. ed. Porto Alegre: +A Educação - Cengage Learning Brasil, 2024. E-book. p.11. ISBN 9786555582208. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9786555582208/. Acesso em: 29 out. 2025.",
    "CASSARRO, Antonio C. Sistemas de informações para tomadas de decisões. 4. ed. Porto Alegre: +A Educação - Cengage Learning Brasil, 2024. E-book. p.11. ISBN 9786555582208. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9786555582208/. Acesso em: 29 out. 2025.",
    "FILHO, Guilherme F. Automação de Processos e de Sistemas. Rio de Janeiro: Érica, 2014. E-book. p.35. ISBN 9788536518138. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9788536518138/. Acesso em: 29 out. 2025.",
    "JR., Henry C L. Tecnologia da Informação. Rio de Janeiro: LTC, 2006. E-book. p.8. ISBN 978-85-216-2393-9. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/978-85-216-2393-9/. Acesso em: 01 nov. 2025.",
    "JR., Henry C L. Tecnologia da Informação. Rio de Janeiro: LTC, 2006. E-book. p.45. ISBN 978-85-216-2393-9. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/978-85-216-2393-9/. Acesso em: 01 nov. 2025.",
    "JR., Henry C L. Tecnologia da Informação. Rio de Janeiro: LTC, 2006. E-book. p.83. ISBN 978-85-216-2393-9. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/978-85-216-2393-9/. Acesso em: 29 out. 2025.",
    "RAMAKRISHNAN, Raghu; GEHRKE, Johannes. Sistemas de Gerenciamento de Bancos de Dados. 3. ed. Porto Alegre: AMGH, 2008. E-book. p.19. ISBN 9788563308771. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9788563308771/. Acesso em: 29 out. 2025.",
    "RAMAKRISHNAN, Raghu; GEHRKE, Johannes. Sistemas de Gerenciamento de Bancos de Dados. 3. ed. Porto Alegre: AMGH, 2008. E-book. p.28. ISBN 9788563308771. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9788563308771/. Acesso em: 29 out. 2025.",
    "RAMAKRISHNAN, Raghu; GEHRKE, Johannes. Sistemas de Gerenciamento de Bancos de Dados. 3. ed. Porto Alegre: AMGH, 2008. E-book. p.31. ISBN 9788563308771. Disponível em: https://integrada.minhabiblioteca.com.br/reader/books/9788563308771/. Acesso em: 29 out. 2025.",
]


def build_pdf():
    OUT_DIR.mkdir(exist_ok=True)
    styles = style_sheet()
    doc = SimpleDocTemplate(
        str(PDF_PATH),
        pagesize=A4,
        leftMargin=3 * cm,
        rightMargin=2 * cm,
        topMargin=3 * cm,
        bottomMargin=2 * cm,
        title="TCC Corrigido - UniSalas",
        author="Luid Casotti Laurentino",
    )
    story = []

    add_center(story, "UNINORTE – UNIÃO EDUCACIONAL DO NORTE<br/>SISTEMAS DE INFORMAÇÃO", styles, True, 2.2 * cm)
    add_center(story, "LUID CASOTTI LAURENTINO", styles, True, 3.5 * cm)
    add_center(story, "UNISALAS: AUTOMATIZAÇÃO DO PROCESSO DE RESERVA DE SALAS<br/>ACADÊMICAS POR MEIO DE UM SISTEMA WEB", styles, True, 7.2 * cm)
    add_center(story, "RIO BRANCO<br/>2025", styles, True)
    story.append(PageBreak())

    add_center(story, "LUID CASOTTI LAURENTINO", styles, True, 3.8 * cm)
    add_center(story, "UNISALAS: AUTOMATIZAÇÃO DO PROCESSO DE RESERVA DE SALAS<br/>ACADÊMICAS POR MEIO DE UM SISTEMA WEB", styles, True, 2.0 * cm)
    story.append(Spacer(1, 1.2 * cm))
    story.append(
        Table(
            [[
                "",
                para(
                    "Trabalho de Conclusão de Curso apresentado ao curso de Sistemas de Informação da UniNorte - Centro Universitário do Norte, como requisito parcial para obtenção do grau de Bacharel em Sistemas de Informação.<br/><br/>Orientador: Prof. Rodrigo Garcia",
                    styles["BodyTCC"],
                ),
            ]],
            colWidths=[6.0 * cm, 8.0 * cm],
        )
    )
    story.append(Spacer(1, 6.0 * cm))
    add_center(story, "RIO BRANCO<br/>2025", styles, True)
    story.append(PageBreak())

    story.append(para("RESUMO", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "Este Trabalho de Conclusão de Curso apresenta o desenvolvimento do UniSalas, um sistema web destinado à organização do processo de solicitação e gerenciamento de reservas de salas acadêmicas. A aplicação foi implementada com Laravel, PHP, Blade, Bootstrap e banco de dados MySQL/MariaDB, contemplando dois perfis principais de acesso: administrador e professor.",
            "O sistema permite que professores solicitem reservas simples ou recorrentes, consultem suas solicitações, acompanhem respostas administrativas, desistam de reservas específicas e troquem mensagens com a administração. Para o administrador, foram implementados recursos de gerenciamento de usuários, blocos, salas, manutenção de espaços, análise de reservas, aprovação ou recusa individual e em lote, histórico filtrável, publicação de avisos e troca de mensagens diretas.",
            "A solução reduz a dependência de controles manuais ao centralizar informações sobre salas, períodos, status das solicitações, comentários e indisponibilidades por manutenção. O sistema também executa regras de negócio para verificar conflitos, cancelar reservas afetadas por manutenção, aprovar solicitações em situações específicas e registrar respostas aos professores. Dessa forma, o UniSalas contribui para maior organização, rastreabilidade e eficiência no uso dos espaços acadêmicos.",
            "Palavras-chave: Reserva de salas; Sistema web; Laravel; Gestão acadêmica; Automação.",
        ],
    )
    story.append(PageBreak())

    story.append(para("ABSTRACT", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "This final course project presents UniSalas, a web-based system designed to organize classroom reservation requests and management. The application was implemented using Laravel, PHP, Blade, Bootstrap, and a MySQL/MariaDB database, supporting two main access profiles: administrator and teacher.",
            "The system allows teachers to request single or recurring reservations, view their requests, follow administrative responses, cancel specific reservations, and exchange direct messages with the administration. Administrators can manage users, buildings, rooms, maintenance periods, reservation analysis, individual or batch approval and rejection, filtered history, announcements, and direct messages.",
            "The solution reduces dependence on manual controls by centralizing information about rooms, periods, request statuses, comments, and maintenance-related unavailability. It also applies business rules for conflict checking, maintenance-based cancellation, specific automatic approvals, and administrative responses to teachers. Therefore, UniSalas improves organization, traceability, and efficiency in the use of academic spaces.",
            "Keywords: Room reservation; Web system; Laravel; Academic management; Automation.",
        ],
    )
    story.append(PageBreak())

    story.append(para("SUMÁRIO", styles["Chapter"]))
    for item in [
        "1. INTRODUÇÃO",
        "2. DESENVOLVIMENTO",
        "2.1. LEVANTAMENTO DE REQUISITOS",
        "2.2. ESTRUTURA DO SISTEMA",
        "2.3. DESENVOLVIMENTO E IMPLEMENTAÇÃO",
        "2.4. PROBLEMATIZAÇÃO",
        "2.5. METODOLOGIAS ADOTADAS",
        "2.6. RESULTADOS OBTIDOS E BENEFÍCIOS",
        "3. CONCLUSÃO",
        "REFERÊNCIAS",
        "APÊNDICE A – Diagrama de Classes do Sistema UniSalas",
        "APÊNDICE B – Diagrama de Casos de Uso do Sistema UniSalas",
        "APÊNDICE C – Protótipos de Alta Fidelidade do Sistema UniSalas",
    ]:
        story.append(para(item, styles["Sumario"]))
    story.append(PageBreak())

    story.append(para("1. INTRODUÇÃO", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "A organização e o gerenciamento de salas de aula são aspectos fundamentais para o bom funcionamento de uma instituição de ensino. No entanto, muitas vezes esse processo é realizado de forma manual e desordenada, o que pode gerar conflitos de horários, atrasos e dificuldades na comunicação entre professores e administração. Na instituição Uniorte, essa realidade ocasiona situações em que docentes precisam buscar alternativas de última hora, comprometendo o andamento das atividades acadêmicas.",
            "Historicamente, a adoção de tecnologia sempre gerou discussões sobre seu impacto na gestão.",
        ],
    )
    story.append(para("Nos primórdios da era da tecnologia, as empresas automatizaram processos manuais, geralmente economizando mão-de-obra e reduzindo o tempo do ciclo de processamento. Alguns gerentes e acadêmicos se preocuparam com o impacto da tecnologia sobre a organização. Como os sistemas de informação alterariam os empregos das pessoas e a natureza do trabalho? Qual seria o impacto geral sobre a firma? Haveria uma redução nas camadas administrativas? Os trabalhadores que permaneceram na empresa achariam que seu trabalho foi aumentado ou diminuído pelos computadores? Na sua maior parte, os estudos sobre o impacto dos computadores nas organizações produziram ambos os resultados.” (JR., 2006, p.45)", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "Diante dessa problemática, este trabalho apresenta o UniSalas, um sistema web desenvolvido com o objetivo de organizar e semiautomatizar o processo de solicitação e gerenciamento de reservas de salas acadêmicas. A automação é a chave para superar desafios logísticos da instituição.",
        ],
    )
    story.append(para("“A automação de sistemas visa reduzir a intervenção humana nessas atividades e, com isso, aumentar a produtividade, qualidade, eficiência e redução de custos. Esses softwares visam à automação da cadeia de informações dos sistemas de produção (coleta, transmissão, análise, armazenamento e distribuição).” (FILHO, 2014, p. 35).", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "Assim, o sistema permite que professores solicitem reservas simples ou recorrentes, acompanhem o status das solicitações e recebam respostas administrativas. A plataforma também contribui para a gestão institucional ao centralizar usuários, blocos, salas, manutenção, avisos, mensagens e histórico de reservas. O desenvolvimento do projeto segue princípios de metodologias ágeis, buscando simplicidade, eficiência, rastreabilidade e fácil manutenção.",
            "Com a implantação do sistema, busca-se reduzir a desorganização e otimizar o uso das salas de aula, trazendo benefícios diretos para professores, alunos e gestores. Dessa forma, o UniSalas representa uma solução prática para um problema cotidiano no ambiente acadêmico.",
        ],
    )

    story.append(para("2. DESENVOLVIMENTO", styles["Chapter"]))
    story.append(para("2.1. LEVANTAMENTO DE REQUISITOS", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "O ponto de partida do projeto foi a identificação dos problemas existentes na instituição, observando a dificuldade dos professores em encontrar salas disponíveis e o tempo perdido com a falta de controle centralizado. A partir dessa análise, foram levantados os requisitos funcionais e não funcionais do sistema.",
            "Os requisitos funcionais incluem autenticação de usuários, separação de perfis entre administrador e professor, cadastro de usuários, gerenciamento de blocos e salas, controle de manutenção, solicitação de reservas simples e recorrentes, verificação de disponibilidade, aprovação ou recusa de reservas, aprovação em lote de recorrências, cancelamento de conflitos, publicação de avisos, mensagens diretas, histórico e acompanhamento das solicitações pelo professor.",
            "Os requisitos não funcionais envolvem usabilidade, segurança de sessão, controle de acesso por perfil, uso de banco de dados relacional, organização em arquitetura MVC, interface responsiva, rastreabilidade por status e timestamps, além de manutenção facilitada por meio dos padrões do framework Laravel.",
        ],
    )
    story.append(para("2.2. ESTRUTURA DO SISTEMA", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "O sistema foi dividido em dois níveis de acesso: administrador e professor. O acesso é realizado por autenticação Laravel e as rotas administrativas são protegidas por middleware específico.",
            "O administrador é responsável por gerenciar usuários, blocos, salas, manutenção, reservas, avisos, mensagens e histórico geral. Na fila de aprovação, o administrador pode analisar reservas individuais ou grupos recorrentes, registrando aprovação, recusa ou cancelamento com comentários administrativos.",
            "O professor pode acessar seu painel, solicitar novas reservas, consultar suas reservas, visualizar histórico, desistir de reservas específicas e trocar mensagens com a administração. A interface do sistema foi organizada em telas Blade por domínio funcional, com layout comum, menu lateral e indicadores visuais de mensagens e respostas.",
        ],
    )
    story.append(para("2.3. DESENVOLVIMENTO E IMPLEMENTAÇÃO", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "O UniSalas foi implementado com PHP 8.2 e Laravel 12. O projeto utiliza rotas web, controllers, models Eloquent, migrations e views Blade. O front-end emprega Bootstrap 5, Font Awesome e SweetAlert2, além de configuração com Vite, Tailwind CSS e Axios.",
            "A camada de controle é composta principalmente por AuthController e AdminController. O AuthController concentra login e logout, incluindo validação de credenciais, regeneração de sessão e redirecionamento por perfil. O AdminController concentra os fluxos operacionais do sistema, incluindo usuários, blocos, salas, reservas, manutenção, histórico, avisos e mensagens.",
            "Os models principais são User, Bloco, Sala, Reserva, Aviso, MensagemDireta e NotificacaoVisualizada. O banco de dados está configurado para MySQL/MariaDB, com tabelas para usuários, blocos, salas, reservas, avisos, mensagens diretas e estruturas padrão do Laravel, como sessões, cache e jobs.",
        ],
    )
    story.append(para("2.4. PROBLEMATIZAÇÃO", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "A organização das salas de aula é um fator essencial para o bom funcionamento de qualquer instituição de ensino. No entanto, na Uniorte, observa-se que o processo de agendamento de salas pode gerar transtornos quando é feito sem centralização, como conflitos de horários, atrasos nas aulas e dificuldade de controle por parte da administração.",
            "Essa dificuldade reflete uma lacuna na gestão dos recursos, cuja avaliação é fundamental para a melhoria contínua.",
        ],
    )
    story.append(para("Graças ao processo de avaliação de resultados é que poderemos determinar a necessidade ou não de alteração de planos, de estratégias, da organização dos recursos e mesmo dos próprios objetivos. Conforme demonstramos no quadro a seguir, a tarefa de controle e avaliação não é, nem deve ser, executada como atribuição estática, final. Trata-se de um processo que deve ser executado continuamente, durante a realização de cada tarefa e, consoante as modernas técnicas de gestão (TQC – qualidade total por toda a empresa, por exemplo), ser de responsabilidade de cada executor. O ser humano é que gera qualidade, no ato da realização de suas ações, e não mais, como era conceito antigo, deixar a cargo de uma área de controle verificar a qualidade dos serviços/produtos decorrentes de cada tarefa.” (CASSARRO, 2024, p.11)", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "Diante desse cenário, a questão norteadora deste trabalho é: como desenvolver um sistema web capaz de organizar o processo de reserva de salas acadêmicas, tornando-o mais ágil, rastreável e acessível aos professores e administradores?",
            "O UniSalas responde a esse problema por meio de um fluxo digital centralizado. O professor registra a solicitação e o sistema verifica conflito por sala, data e período. Quando a sala ou o bloco está em manutenção, a solicitação recebe tratamento específico. As reservas passam a possuir status como pendente, em análise, aprovada, rejeitada e cancelada, preservando o controle administrativo e evitando sobreposições indevidas.",
        ],
    )
    story.append(para("Tabela 1 - Soluções Desenvolvidas", styles["Caption"]))
    add_table(story, styles)

    story.append(para("2.5. METODOLOGIAS ADOTADAS", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "Para o desenvolvimento do UniSalas, foram aplicados conceitos essenciais da metodologia ágil Extreme Programming (XP), que se adapta bem tanto a projetos pequenos quanto a médios e grandes. Essa metodologia foi escolhida por priorizar simplicidade, flexibilidade, comunicação e entrega contínua de valor ao usuário final.",
            "Os principais fundamentos do XP aplicados neste projeto foram revisão de código, refatoração contínua, simplicidade, iterações curtas e testes de código. Essas práticas contribuíram para um processo de desenvolvimento mais organizado, com foco na qualidade do software e na identificação progressiva de ajustes.",
            "Na etapa de implementação, foram desenvolvidos os módulos de autenticação, usuários, blocos, salas, manutenção, reservas simples e recorrentes, painel administrativo, painel do professor, avisos, mensagens e histórico. A escolha pelo desenvolvimento web é estratégica, dada a tendência de acesso via navegador.",
        ],
    )
    story.append(para("O fato histórico mais significativo, talvez, seja a entrada dos SGBDs na Era Internet. Enquanto a primeira geração de websites armazenava seus dados exclusivamente em arquivos dos sistemas operacionais, o uso de um SGBD para armazenar dados acessados através de um navegador Web tem se difundido cada vez mais.” (RAMAKRISHNAN; GEHRKE, 2008, p. 31).", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "O banco de dados utilizado é MySQL/MariaDB, fundamental para a gestão das informações acadêmicas. Segundo Ramakrishnan e Gehrke (2008, p. 28), um banco de dados é uma coleção de dados que descreve atividades de organizações relacionadas. Portanto, a modelagem do banco de dados é essencial para que o sistema gerencie corretamente usuários, salas, horários, status e mensagens.",
        ],
    )

    story.append(para("2.6. RESULTADOS OBTIDOS E BENEFÍCIOS", styles["Section"]))
    add_paragraphs(
        story,
        styles,
        [
            "Com o desenvolvimento do UniSalas, foi obtido um sistema funcional para apoiar a gestão de reservas acadêmicas. A aplicação permite organizar a infraestrutura em blocos e salas, controlar manutenção, cadastrar usuários, receber solicitações, analisar reservas e manter histórico das decisões.",
            "Para professores, os principais benefícios são a solicitação digital de reservas, a possibilidade de recorrência, o acompanhamento de status, o recebimento de respostas administrativas e a comunicação por mensagens. Para administradores, destacam-se a centralização de solicitações, a análise de conflitos, a aprovação em lote de recorrências, os avisos institucionais e o histórico filtrável.",
            "A centralização e o acesso eficiente aos dados são fundamentais, pois, conforme Ramakrishnan e Gehrke (2008, p. 19), os sistemas de gerenciamento de banco de dados são ferramentas indispensáveis para gerenciar informações. O sistema ainda não possui relatórios gerenciais analíticos completos ou exportáveis, mas já apresenta dashboard com contadores, histórico por filtros e registros suficientes para acompanhamento operacional.",
        ],
    )

    story.append(para("3. CONCLUSÃO", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "O desenvolvimento do UniSalas resultou em uma aplicação web capaz de organizar o processo de reserva de salas acadêmicas, substituindo parte significativa do controle manual por um fluxo digital centralizado. O sistema implementado oferece recursos para professores e administradores, contemplando autenticação, gerenciamento de infraestrutura, solicitação de reservas, aprovação administrativa, histórico, avisos, mensagens e controle de manutenção.",
            "A solução contribui para a redução de conflitos de agendamento ao verificar sala, data e período, além de registrar status e comentários administrativos. O tratamento de reservas recorrentes, a decisão em lote e o cancelamento de reservas afetadas por manutenção ampliam a aderência do sistema às necessidades reais de uso institucional.",
            "A importância da Tecnologia da Informação na gestão e na competitividade das organizações é inegável, especialmente em contextos dinâmicos como o acadêmico.",
        ],
    )
    story.append(para("Nos últimos anos, seis tendências importantes alteraram a forma através da qual as organizações usam a tecnologia. Estas tendências tornam imperativo que um administrador se familiarize tanto com o uso da tecnologia quanto como controlá-la na organização.” (JR., 2006, p.8)", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "A utilização de tecnologias modernas no projeto garante potencial para desempenho adequado e flexibilidade para futuras expansões, como integração com sistemas acadêmicos, criação de relatórios gerenciais completos, exportação de dados e ampliação dos testes automatizados.",
        ],
    )
    story.append(para("Obter valor da TI é importante para organizações sobreviverem e florescerem na economia altamente competitiva do século XXI. Muitos acreditam que a tecnologia da informação possui a chave do, pois as empresas desenvolvem sistemas que lhes conferem uma vantagem competitiva. A TI também permite que os administradores concebam estruturas novas e dinâmicas de organização para competir de forma mais eficaz. As firmas que criarem valor através da tecnologia da informação serão as vencedoras no próximo século. (JR., 2006, p. 83).", styles["QuoteTCC"]))
    add_paragraphs(
        story,
        styles,
        [
            "Portanto, o sistema de agendamento de salas desenvolvido neste projeto demonstra como a tecnologia pode ser aplicada para resolver problemas práticos e melhorar a eficiência organizacional da Uniorte. Ao substituir o modelo manual por um ambiente digital, a instituição obtém ganhos em agilidade, segurança, comunicação e controle. O projeto também reforça a importância da transformação digital e da gestão da inovação, incentivando uma mudança cultural voltada à eficiência e à sustentabilidade do ambiente acadêmico.",
        ],
    )
    story.append(PageBreak())

    story.append(para("REFERÊNCIAS", styles["Chapter"]))
    for ref in REFERENCES:
        story.append(para(ref, styles["RefTCC"]))
    story.append(PageBreak())

    story.append(para("APÊNDICE A – Diagrama de Classes do Sistema UniSalas", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "Este apêndice apresenta o Diagrama de Classes Detalhado do sistema UniSalas. O diagrama deve representar as principais entidades implementadas no sistema, incluindo User, Bloco, Sala, Reserva, Aviso, MensagemDireta e NotificacaoVisualizada, além de seus atributos e relacionamentos.",
            "A estrutura atual contempla relações como usuário com muitas reservas, bloco com muitas salas, sala pertencente a um bloco e reserva pertencente a usuário e sala. Também devem ser considerados os campos de manutenção em blocos e salas, bem como os campos de recorrência e comentários nas reservas.",
        ],
    )
    story.append(para("Figura 1 – Diagrama de Classes", styles["Caption"]))
    story.append(Spacer(1, 6.5 * cm))

    story.append(para("APÊNDICE B – Diagrama de Casos de Uso do Sistema UniSalas", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "O Diagrama de Casos de Uso descreve o comportamento funcional do sistema UniSalas sob a perspectiva dos usuários. O diagrama define os limites do sistema e as funcionalidades oferecidas aos dois principais atores: Docente e Administrador.",
            "Além dos casos de uso de agendar sala e gerenciar usuários, a versão atual deve contemplar: gerenciar blocos, gerenciar salas, controlar manutenção, solicitar reserva recorrente, aprovar ou recusar reservas, aprovar recorrências em lote, publicar avisos, enviar mensagens diretas, consultar histórico e desistir de reserva.",
        ],
    )
    story.append(para("Figura 1 – Diagrama de Casos de Uso", styles["Caption"]))
    story.append(Spacer(1, 4.5 * cm))

    story.append(para("APÊNDICE C – Protótipos de Alta Fidelidade do Sistema UniSalas", styles["Chapter"]))
    add_paragraphs(
        story,
        styles,
        [
            "Este apêndice apresenta os Protótipos de Alta Fidelidade desenvolvidos, que representam a base visual inicial do sistema UniSalas. Os protótipos simulam a experiência do usuário e serviram como referência para a construção das telas principais antes e durante a implementação.",
            "A versão navegável do protótipo pode ser acessada através do endereço: https://square-stuck-54004199.figma.site. Acesso em: 08 nov. 2025.",
        ],
    )
    story.append(para("Figura 1 – Tela de Gerenciamento Administrativo", styles["Caption"]))
    add_paragraphs(
        story,
        styles,
        [
            "Esta tela serve como centro de controle do sistema, sendo exclusiva para administradores da Uniorte. Na versão implementada, esse controle foi ampliado para incluir dashboard, usuários, blocos, salas, manutenção, reservas, histórico, avisos e mensagens.",
            "Elaborado pelo autor (2025). Disponível em: https://square-stuck-54004199.figma.site. Acesso em: 08 nov. 2025.",
        ],
    )
    story.append(Spacer(1, 2.0 * cm))
    story.append(para("Figura 2 – Protótipo do Painel Principal do Docente", styles["Caption"]))
    add_paragraphs(
        story,
        styles,
        [
            "Esta interface funciona como o painel de controle central para o professor após o login. Ela foi projetada para ser intuitiva, fornecendo acesso rápido às funcionalidades essenciais e uma visão imediata das solicitações e comunicações.",
            "Os botões de destaque levam às ações principais: reservar sala e minhas reservas. Na versão atual, o professor também pode consultar histórico, acompanhar respostas administrativas, solicitar recorrência e trocar mensagens diretas.",
            "Elaborado pelo autor (2025). Disponível em: https://square-stuck-54004199.figma.site. Acesso em: 08 nov. 2025.",
        ],
    )

    doc.build(story)
    return PDF_PATH


def render_last_pages():
    out = OUT_DIR / "render_check_corrigido"
    out.mkdir(exist_ok=True)
    doc = fitz.open(PDF_PATH)
    for index in range(doc.page_count):
        if index in {0, 2, 4, doc.page_count - 1}:
            pix = doc[index].get_pixmap(matrix=fitz.Matrix(1.5, 1.5), alpha=False)
            pix.save(out / f"page_{index + 1}.png")
    print(f"pages={doc.page_count}")


if __name__ == "__main__":
    print(build_pdf())
    render_last_pages()
