import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn
import os

def create_element(name):
    return OxmlElement(name)

def set_cell_background(cell, fill_hex):
    tcPr = cell._element.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=140, bottom=140, left=180, right=180):
    tcPr = cell._element.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def set_cell_border(cell, **kwargs):
    """
    kwargs can be top, bottom, left, right.
    val: 'single', 'dashed', 'dotted', etc.
    color: hex string, e.g. 'CBD5E1'
    sz: size in eighths of a pt (e.g. '4', '8', '12')
    """
    tcPr = cell._element.get_or_add_tcPr()
    tcBorders = parse_xml(f'<w:tcBorders {nsdecls("w")}/>')
    for edge in ('top', 'left', 'bottom', 'right', 'insideH', 'insideV'):
        edge_data = kwargs.get(edge)
        if edge_data:
            tag = 'w:{}'.format(edge)
            element = parse_xml(f'<{tag} {nsdecls("w")} w:val="{edge_data.get("val", "single")}" w:sz="{edge_data.get("sz", "4")}" w:space="0" w:color="{edge_data.get("color", "auto")}"/>')
            tcBorders.append(element)
        else:
            tag = 'w:{}'.format(edge)
            element = parse_xml(f'<{tag} {nsdecls("w")} w:val="none"/>')
            tcBorders.append(element)
    tcPr.append(tcBorders)

def add_heading_1(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(18)
    p.paragraph_format.space_after = Pt(8)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(16)
    run.font.bold = True
    run.font.color.rgb = RGBColor(30, 41, 59) # Slate-800
    
    # Add bottom accent line under H1
    pBdr = parse_xml(f'<w:pBdr {nsdecls("w")}><w:bottom w:val="single" w:sz="12" w:space="4" w:color="4F46E5"/></w:pBdr>')
    p._element.get_or_add_pPr().append(pBdr)
    return p

def add_heading_2(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(14)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(13)
    run.font.bold = True
    run.font.color.rgb = RGBColor(79, 70, 229) # Indigo-600
    return p

def add_heading_3(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(10)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(11)
    run.font.bold = True
    run.font.color.rgb = RGBColor(14, 165, 233) # Sky-600
    return p

def add_body_p(doc, text="", bold_prefix="", italic_suffix=""):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = 'Calibri'
        r_pre.font.size = Pt(10.5)
        r_pre.font.bold = True
        r_pre.font.color.rgb = RGBColor(30, 41, 59)
    if text:
        r_text = p.add_run(text)
        r_text.font.name = 'Calibri'
        r_text.font.size = Pt(10.5)
        r_text.font.color.rgb = RGBColor(51, 65, 85) # Slate-700
    if italic_suffix:
        r_it = p.add_run(italic_suffix)
        r_it.font.name = 'Calibri'
        r_it.font.size = Pt(10)
        r_it.font.italic = True
        r_it.font.color.rgb = RGBColor(100, 116, 139) # Slate-500
    return p

def add_bullet_p(doc, text, bold_prefix=""):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = 'Calibri'
        r_pre.font.size = Pt(10.5)
        r_pre.font.bold = True
        r_pre.font.color.rgb = RGBColor(30, 41, 59)
    r_text = p.add_run(text)
    r_text.font.name = 'Calibri'
    r_text.font.size = Pt(10.5)
    r_text.font.color.rgb = RGBColor(51, 65, 85)
    return p

def add_screenshot_placeholder(doc, title, instructions, caption):
    """
    Creates a beautiful, rounded/bordered placeholder box for screenshots.
    """
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl.autofit = False
    
    cell = tbl.cell(0, 0)
    cell.width = Inches(6.2)
    set_cell_background(cell, "F8FAFC") # Soft Slate light background
    set_cell_margins(cell, top=180, bottom=180, left=240, right=240)
    
    # Border styling (Indigo left bar + Slate borders)
    set_cell_border(cell, 
                    left={'val': 'single', 'sz': '24', 'color': '4F46E5'},
                    top={'val': 'single', 'sz': '4', 'color': 'CBD5E1'},
                    bottom={'val': 'single', 'sz': '4', 'color': 'CBD5E1'},
                    right={'val': 'single', 'sz': '4', 'color': 'CBD5E1'})
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(4)
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    
    r_icon = p.add_run("📸  [ TEMPAT SCREENSHOT ]\n")
    r_icon.font.name = 'Calibri'
    r_icon.font.size = Pt(11)
    r_icon.font.bold = True
    r_icon.font.color.rgb = RGBColor(79, 70, 229)
    
    r_title = p.add_run(f"{title}\n")
    r_title.font.name = 'Calibri'
    r_title.font.size = Pt(11)
    r_title.font.bold = True
    r_title.font.color.rgb = RGBColor(15, 23, 42)
    
    r_inst = p.add_run(f"Instruksi Petunjuk: {instructions}\n\n(Tempel/Insert gambar screenshot Anda di area kotak ini)")
    r_inst.font.name = 'Calibri'
    r_inst.font.size = Pt(9.5)
    r_inst.font.italic = True
    r_inst.font.color.rgb = RGBColor(100, 116, 139)
    
    # Caption under table
    p_cap = doc.add_paragraph()
    p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap.paragraph_format.space_before = Pt(4)
    p_cap.paragraph_format.space_after = Pt(12)
    r_cap = p_cap.add_run(f"Gambar: {caption}")
    r_cap.font.name = 'Calibri'
    r_cap.font.size = Pt(9.5)
    r_cap.font.bold = True
    r_cap.font.italic = True
    r_cap.font.color.rgb = RGBColor(71, 85, 105)

def format_table(table, col_widths, headers, data):
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    
    # Header row
    hdr_cells = table.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].width = Inches(col_widths[i])
        set_cell_background(hdr_cells[i], "1E293B") # Dark Slate Header
        set_cell_margins(hdr_cells[i], top=120, bottom=120, left=140, right=140)
        set_cell_border(hdr_cells[i], 
                        bottom={'val': 'single', 'sz': '12', 'color': '4F46E5'},
                        top={'val': 'single', 'sz': '4', 'color': '334155'},
                        left={'val': 'single', 'sz': '4', 'color': '334155'},
                        right={'val': 'single', 'sz': '4', 'color': '334155'})
        p = hdr_cells[i].paragraphs[0]
        p.paragraph_format.space_after = Pt(0)
        r = p.add_run(title)
        r.font.name = 'Calibri'
        r.font.size = Pt(10)
        r.font.bold = True
        r.font.color.rgb = RGBColor(255, 255, 255)
        
    # Data rows
    for r_idx, row_data in enumerate(data):
        row_cells = table.add_row().cells
        bg_color = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, val in enumerate(row_data):
            row_cells[c_idx].width = Inches(col_widths[c_idx])
            set_cell_background(row_cells[c_idx], bg_color)
            set_cell_margins(row_cells[c_idx], top=100, bottom=100, left=140, right=140)
            set_cell_border(row_cells[c_idx],
                            bottom={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                            top={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                            left={'val': 'single', 'sz': '4', 'color': 'E2E8F0'},
                            right={'val': 'single', 'sz': '4', 'color': 'E2E8F0'})
            p = row_cells[c_idx].paragraphs[0]
            p.paragraph_format.space_after = Pt(0)
            r = p.add_run(str(val))
            r.font.name = 'Calibri'
            r.font.size = Pt(9.5)
            r.font.color.rgb = RGBColor(51, 65, 85)

print("Helper script ready.")
