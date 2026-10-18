import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def draw_rounded_rect_with_notches(draw, box, radius, notch_x, notch_r, fill, outline, width=1):
    x0, y0, x1, y1 = box
    # Draw rounded rectangle
    draw.rounded_rectangle(box, radius=radius, fill=fill, outline=outline, width=width)
    # Cut out top notch
    draw.ellipse((notch_x - notch_r, y0 - notch_r, notch_x + notch_r, y0 + notch_r), fill=(10, 15, 29, 255), outline=outline, width=width)
    # Cut out bottom notch
    draw.ellipse((notch_x - notch_r, y1 - notch_r, notch_x + notch_r, y1 + notch_r), fill=(10, 15, 29, 255), outline=outline, width=width)

def create_clean_banner():
    WIDTH, HEIGHT = 1920, 720
    
    # 1. Base Dark Luxury Gradient Canvas
    canvas = Image.new('RGBA', (WIDTH, HEIGHT), (10, 15, 29, 255))
    draw = ImageDraw.Draw(canvas)
    
    # Soft vertical/radial gradient
    for y in range(HEIGHT):
        ratio = y / HEIGHT
        r = int(10 * (1 - ratio*0.5))
        g = int(15 * (1 - ratio*0.5))
        b = int(29 * (1 - ratio*0.4))
        draw.line([(0, y), (WIDTH, y)], fill=(r, g, b, 255))
        
    # Ambient Glows (Very subtle, sophisticated, not sci-fi neon)
    glow_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    glow_draw = ImageDraw.Draw(glow_layer)
    
    # Left ambient soft blue
    for r in range(400, 40, -20):
        alpha = int(25 * (1 - r / 400))
        glow_draw.ellipse((400 - r, 360 - r, 400 + r, 360 + r), fill=(2, 132, 199, alpha))
        
    # Right ambient soft indigo
    for r in range(450, 50, -25):
        alpha = int(15 * (1 - r / 450))
        glow_draw.ellipse((1400 - r, 300 - r, 1400 + r, 300 + r), fill=(59, 130, 246, alpha))
        
    glow_layer = glow_layer.filter(ImageFilter.GaussianBlur(50))
    canvas = Image.alpha_composite(canvas, glow_layer)
    draw = ImageDraw.Draw(canvas)
    
    # Subtle dot matrix / grid pattern for authentic modern tech feel
    for x in range(40, WIDTH, 48):
        for y in range(40, HEIGHT, 48):
            draw.point((x, y), fill=(148, 163, 184, 25))
            
    # Fonts
    font_bold_candidates = [
        r"C:\Windows\Fonts\segoeuib.ttf",
        r"C:\Windows\Fonts\arialbd.ttf",
    ]
    font_reg_candidates = [
        r"C:\Windows\Fonts\segoeui.ttf",
        r"C:\Windows\Fonts\arial.ttf",
    ]
    
    bold_path = next((f for f in font_bold_candidates if os.path.exists(f)), None)
    reg_path = next((f for f in font_reg_candidates if os.path.exists(f)), None)
    
    font_title = ImageFont.truetype(bold_path, 48) if bold_path else ImageFont.load_default()
    font_subhead = ImageFont.truetype(reg_path, 21) if reg_path else ImageFont.load_default()
    font_tag = ImageFont.truetype(bold_path, 15) if bold_path else ImageFont.load_default()
    font_ticket_bold = ImageFont.truetype(bold_path, 17) if bold_path else ImageFont.load_default()
    font_ticket_sm = ImageFont.truetype(reg_path, 13) if reg_path else ImageFont.load_default()
    font_ticket_mono = ImageFont.truetype(reg_path, 11) if reg_path else ImageFont.load_default()
    font_badge = ImageFont.truetype(bold_path, 14) if bold_path else ImageFont.load_default()

    # 2. Authentic VIP Event Ticket Pass (Left side)
    # Ticket dimensions: 540 x 360 px
    t_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    t_draw = ImageDraw.Draw(t_layer)
    
    tx0, ty0 = 130, 180
    tw, th = 540, 360
    tx1, ty1 = tx0 + tw, ty0 + th
    notch_x = tx0 + 360
    notch_r = 16
    
    # Ticket Drop Shadow
    shadow_box = (tx0 + 10, ty0 + 15, tx1 + 10, ty1 + 20)
    t_draw.rounded_rectangle(shadow_box, radius=24, fill=(0, 0, 0, 120))
    t_layer = t_layer.filter(ImageFilter.GaussianBlur(16))
    canvas = Image.alpha_composite(canvas, t_layer)
    
    # Draw ticket body on separate layer for crisp edges
    ticket_img = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    tk_draw = ImageDraw.Draw(ticket_img)
    
    # Card background: Sleek Dark Slate Gradient
    tk_draw.rounded_rectangle((tx0, ty0, tx1, ty1), radius=22, fill=(15, 23, 42, 245), outline=(71, 85, 105, 120), width=1)
    
    # Perforated dashed tear line
    for dash_y in range(ty0 + notch_r + 10, ty1 - notch_r - 10, 12):
        tk_draw.line([(notch_x, dash_y), (notch_x, dash_y + 6)], fill=(100, 116, 139, 140), width=2)
        
    # Cut out notch circles with dark bg fill and outline
    tk_draw.ellipse((notch_x - notch_r, ty0 - notch_r, notch_x + notch_r, ty0 + notch_r), fill=(10, 15, 29, 255), outline=(71, 85, 105, 120), width=1)
    tk_draw.ellipse((notch_x - notch_r, ty1 - notch_r, notch_x + notch_r, ty1 + notch_r), fill=(10, 15, 29, 255), outline=(71, 85, 105, 120), width=1)
    
    # Holographic / Gradient Accent Stripe on top of ticket
    for i in range(12):
        ratio = i / 12
        r = int(56 + (168 - 56) * ratio)
        g = int(189 + (85 - 189) * ratio)
        b = int(248 + (247 - 248) * ratio)
        tk_draw.line([(tx0 + 24, ty0 + 24 + i), (notch_x - 24, ty0 + 24 + i)], fill=(r, g, b, 200), width=1)
        
    # Ticket Header Info
    tk_draw.text((tx0 + 24, ty0 + 44), "VIP ALL-ACCESS PASS", fill=(56, 189, 248, 255), font=font_ticket_bold)
    tk_draw.text((tx0 + 24, ty0 + 70), "FESTIVAL & LIVE CONCERT 2026", fill=(241, 245, 249, 255), font=font_ticket_bold)
    
    # Details Grid
    labels = [
        ("KATEGORI", "VIP FESTIVAL"),
        ("TANGGAL", "OFFICIAL EVENT"),
        ("GATE / PINTU", "GATE A - FAST LANE"),
        ("STATUS", "TERVERIFIKASI RESMI")
    ]
    cur_dy = ty0 + 108
    for lbl, val in labels:
        tk_draw.text((tx0 + 24, cur_dy), lbl, fill=(148, 163, 184, 255), font=font_ticket_mono)
        tk_draw.text((tx0 + 130, cur_dy), val, fill=(226, 232, 240, 255), font=font_ticket_sm)
        cur_dy += 24
        
    # Realistic Vector Barcode Lines at bottom of left stub
    barcode_y0 = ty0 + 225
    barcode_h = 42
    bar_x = tx0 + 24
    pattern = [2, 1, 3, 1, 1, 2, 4, 1, 2, 3, 1, 2, 1, 4, 2, 1, 3, 1, 2, 1, 3, 4, 1, 2, 1, 1, 3, 2, 4, 1, 2, 3, 1, 2, 4, 1, 1, 2, 3]
    for w in pattern:
        tk_draw.rectangle((bar_x, barcode_y0, bar_x + w, barcode_y0 + barcode_h), fill=(241, 245, 249, 230))
        bar_x += w + 2
        if bar_x >= notch_x - 30:
            break
            
    tk_draw.text((tx0 + 24, barcode_y0 + barcode_h + 8), "TICKET NO: STK-2026-984128-VIP", fill=(148, 163, 184, 255), font=font_ticket_mono)
    
    # Right Stub: QR Code & Security Seal
    stub_x = notch_x + 24
    tk_draw.text((stub_x, ty0 + 36), "SCAN CHECK-IN", fill=(56, 189, 248, 255), font=font_ticket_bold)
    tk_draw.text((stub_x, ty0 + 60), "PANITIA RESMI", fill=(148, 163, 184, 255), font=font_ticket_mono)
    
    # Vector QR Code Box
    qr_x = stub_x
    qr_y = ty0 + 88
    qr_s = 120
    tk_draw.rounded_rectangle((qr_x, qr_y, qr_x + qr_s, qr_y + qr_s), radius=12, fill=(255, 255, 255, 255))
    
    # Draw realistic QR finder corners
    def draw_finder(fx, fy):
        tk_draw.rectangle((fx, fy, fx + 28, fy + 28), fill=(15, 23, 42, 255))
        tk_draw.rectangle((fx + 5, fy + 5, fx + 23, fy + 23), fill=(255, 255, 255, 255))
        tk_draw.rectangle((fx + 9, fy + 9, fx + 19, fy + 19), fill=(15, 23, 42, 255))
        
    draw_finder(qr_x + 8, qr_y + 8)
    draw_finder(qr_x + qr_s - 36, qr_y + 8)
    draw_finder(qr_x + 8, qr_y + qr_s - 36)
    
    # Random realistic data dots in QR
    import random
    random.seed(42)
    for qrx in range(qr_x + 8, qr_x + qr_s - 8, 6):
        for qry in range(qr_y + 8, qr_y + qr_s - 8, 6):
            if (qrx < qr_x + 40 and qry < qr_y + 40) or \
               (qrx > qr_x + qr_s - 42 and qry < qr_y + 40) or \
               (qrx < qr_x + 40 and qry > qr_y + qr_s - 42):
                continue
            if random.random() > 0.45:
                tk_draw.rectangle((qrx, qry, qrx + 4, qry + 4), fill=(15, 23, 42, 255))
                
    # Right stub bottom info
    tk_draw.text((stub_x, qr_y + qr_s + 14), "100% TERVERIFIKASI", fill=(34, 197, 94, 255), font=font_ticket_mono)
    tk_draw.text((stub_x, qr_y + qr_s + 32), "SEAT: VIP-A12", fill=(241, 245, 249, 255), font=font_ticket_bold)
    
    # Merge ticket into canvas
    canvas = Image.alpha_composite(canvas, ticket_img)
    draw = ImageDraw.Draw(canvas)
    
    # 3. Add Official SeTiket Logo on top of Left stub
    logo_path = r'c:\laragon\www\FunrunHore\public\images\setiketbg.webp'
    if os.path.exists(logo_path):
        logo_img = Image.open(logo_path).convert('RGBA')
        lw, lh = logo_img.size
        # Scaled logo for ticket
        t_logo_w = 90
        t_logo_h = int(lh * (t_logo_w / lw))
        t_logo_resized = logo_img.resize((t_logo_w, t_logo_h), Image.Resampling.LANCZOS)
        canvas.paste(t_logo_resized, (tx0 + tw - 190, ty0 + 36), t_logo_resized)
        
    # 4. Right Side Editorial Typography & Branding
    CONTENT_X = 770
    cur_y = 135
    
    # Brand row: Big Logo + Pill Tag
    if os.path.exists(logo_path):
        logo_img = Image.open(logo_path).convert('RGBA')
        lw, lh = logo_img.size
        target_lw = 160
        target_lh = int(lh * (target_lw / lw))
        big_logo = logo_img.resize((target_lw, target_lh), Image.Resampling.LANCZOS)
        canvas.paste(big_logo, (CONTENT_X, cur_y), big_logo)
        
        # Pill tag next to logo
        pill_x = CONTENT_X + target_lw + 24
        pill_y = cur_y + 14
        pill_text = "PLATFORM RESMI TIKET & KONSER"
        bbox = draw.textbbox((0, 0), pill_text, font=font_tag)
        pw = bbox[2] - bbox[0] + 36
        ph = 32
        
        draw.rounded_rectangle((pill_x, pill_y, pill_x + pw, pill_y + ph), radius=16, fill=(30, 58, 138, 70), outline=(56, 189, 248, 120), width=1)
        draw.ellipse((pill_x + 12, pill_y + 12, pill_x + 18, pill_y + 18), fill=(56, 189, 248, 255))
        draw.text((pill_x + 24, pill_y + 8), pill_text, fill=(56, 189, 248, 255), font=font_tag)
        
        cur_y += target_lh + 32
    else:
        cur_y += 20
        
    # Headline (Bold, Human Editorial, High Contrast)
    line1 = "Pesan Tiket Konser & Event"
    line2_pre = "Lebih Cepat, "
    line2_hl = "Resmi & Terpercaya"
    
    draw.text((CONTENT_X, cur_y), line1, fill=(255, 255, 255, 255), font=font_title)
    cur_y += 62
    
    draw.text((CONTENT_X, cur_y), line2_pre, fill=(255, 255, 255, 255), font=font_title)
    bbox_pre = draw.textbbox((0, 0), line2_pre, font=font_title)
    w_pre = bbox_pre[2] - bbox_pre[0]
    draw.text((CONTENT_X + w_pre, cur_y), line2_hl, fill=(56, 189, 248, 255), font=font_title)
    cur_y += 74
    
    # Subheadline (Clean, 2 lines max, legible)
    sub1 = "Platform e-ticketing modern untuk festival musik, konser akbar, dan seminar."
    sub2 = "E-ticket QR Code instan, verifikasi panitia cepat tanpa antre, dan proteksi pembayaran 100% aman."
    draw.text((CONTENT_X, cur_y), sub1, fill=(203, 213, 225, 255), font=font_subhead)
    cur_y += 34
    draw.text((CONTENT_X, cur_y), sub2, fill=(148, 163, 184, 255), font=font_subhead)
    cur_y += 56
    
    # 4 Trust Badges
    badges = [
        ("E-Ticket QR Code Instan", (56, 189, 248)),
        ("Check-In Scanner Cepat", (34, 197, 94)),
        ("100% Tiket Terverifikasi", (245, 158, 11)),
        ("Customer Support 24/7", (168, 85, 247))
    ]
    
    bx = CONTENT_X
    for b_text, dot_col in badges:
        b_bbox = draw.textbbox((0, 0), b_text, font=font_badge)
        b_w = b_bbox[2] - b_bbox[0] + 38
        b_h = 36
        
        draw.rounded_rectangle((bx, cur_y, bx + b_w, cur_y + b_h), radius=18, fill=(30, 41, 59, 140), outline=(71, 85, 105, 120), width=1)
        draw.ellipse((bx + 14, cur_y + 14, bx + 22, cur_y + 22), fill=(*dot_col, 255))
        draw.text((bx + 28, cur_y + 9), b_text, fill=(241, 245, 249, 255), font=font_badge)
        
        bx += b_w + 14
        
    # 5. Save in all locations
    dest_dirs = [
        r'c:\laragon\www\FunrunHore\public\images\banners',
        r'c:\laragon\www\FunrunHore\public\storage\banners',
        r'c:\laragon\www\FunrunHore\storage\app\public\banners',
    ]
    for d in dest_dirs:
        os.makedirs(d, exist_ok=True)
        
    final_rgb = canvas.convert('RGB')
    for d in dest_dirs:
        p = os.path.join(d, 'setiket-hero-promo.webp')
        final_rgb.save(p, 'WEBP', quality=95, method=6)
        
    artifact_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_hero_promo_banner_v2.webp'
    final_rgb.save(artifact_path, 'WEBP', quality=95, method=6)
    
    print("SUCCESS: Generated clean vector-style banner at", artifact_path)

if __name__ == '__main__':
    create_clean_banner()
