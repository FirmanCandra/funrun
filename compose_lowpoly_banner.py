import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_banner():
    WIDTH, HEIGHT = 1920, 720
    
    # 1. Load generated low-poly background
    bg_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\lowpoly_banner_bg_1792360011053.jpg'
    img = Image.open(bg_path).convert('RGB')
    
    # Crop the blue banner area (excluding the studio grey margins)
    # Bounding box detected: (43, 185, 1324, 620)
    # Slightly inset by 3px to ensure no grey edge bleed
    crop_box = (46, 188, 1321, 617)
    banner_cropped = img.crop(crop_box)
    
    # Resize to exact 1920 x 720 px
    banner_base = banner_cropped.resize((WIDTH, HEIGHT), Image.Resampling.LANCZOS)
    canvas = banner_base.convert('RGBA')
    
    # 2. Add subtle contrast overlay on the right-hand text zone so text is ultra-crisp and legible
    overlay = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    ov_draw = ImageDraw.Draw(overlay)
    
    # Very gentle vignette / dark blue wash behind text (from x: 750 to 1920)
    for x in range(700, WIDTH):
        alpha = int(75 * ((x - 700) / (WIDTH - 700))**1.2)
        ov_draw.line([(x, 0), (x, HEIGHT)], fill=(5, 15, 38, alpha))
        
    canvas = Image.alpha_composite(canvas, overlay)
    
    # 3. Typography Layer (MATCHING REFERENCE IMAGE 1 - NO PILLS, NO AI SLOP)
    txt_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    draw = ImageDraw.Draw(txt_layer)
    
    # Load fonts
    font_bold = ImageFont.truetype(r"C:\Windows\Fonts\segoeuib.ttf", 46)
    font_script = ImageFont.truetype(r"C:\Windows\Fonts\BRUSHSCI.TTF", 64)
    font_subhead = ImageFont.truetype(r"C:\Windows\Fonts\segoeui.ttf", 19)
    
    CONTENT_X = 1020
    cur_y = 155
    
    # (A) Official SeTiket Logo with soft drop shadow
    logo_path = r'c:\laragon\www\FunrunHore\public\images\setiketbg.webp'
    if os.path.exists(logo_path):
        logo_img = Image.open(logo_path).convert('RGBA')
        lw, lh = logo_img.size
        target_lw = 175
        target_lh = int(lh * (target_lw / lw))
        logo_resized = logo_img.resize((target_lw, target_lh), Image.Resampling.LANCZOS)
        
        # Soft shadow behind logo
        shadow_logo = Image.new('RGBA', (target_lw + 24, target_lh + 24), (0, 0, 0, 0))
        shadow_logo.paste(logo_resized, (12, 12))
        shadow_mask = shadow_logo.split()[3]
        black_shadow = Image.new('RGBA', shadow_logo.size, (0, 0, 0, 150))
        black_shadow.putalpha(shadow_mask)
        black_shadow = black_shadow.filter(ImageFilter.GaussianBlur(6))
        
        canvas.paste(black_shadow, (CONTENT_X - 12, cur_y - 12), black_shadow)
        canvas.paste(logo_resized, (CONTENT_X, cur_y), logo_resized)
        
        cur_y += target_lh + 32
    else:
        cur_y += 30
        
    # (B) Main Headline (Bold, High Impact, Human Graphic Design ala Gambar 1)
    # Line 1: 'Pesan Tiket' in crisp white + 'Konser & Event' in vibrant cyan brush script
    t_pesan = "Pesan Tiket "
    w_pesan = draw.textbbox((0, 0), t_pesan, font=font_bold)[2]
    
    # Subtle drop shadow for headline text
    for dx, dy in [(-1, -1), (1, 1), (-1, 1), (1, -1), (0, 2), (2, 2)]:
        draw.text((CONTENT_X + dx, cur_y + dy), t_pesan, fill=(2, 8, 24, 180), font=font_bold)
        draw.text((CONTENT_X + w_pesan + dx, cur_y - 10 + dy), "Konser & Event", fill=(2, 8, 24, 180), font=font_script)
        
    draw.text((CONTENT_X, cur_y), t_pesan, fill=(255, 255, 255, 255), font=font_bold)
    draw.text((CONTENT_X + w_pesan, cur_y - 10), "Konser & Event", fill=(0, 229, 255, 255), font=font_script)
    cur_y += 58
    
    # Line 2: 'Resmi, Cepat & Terpercaya'
    t_line2 = "Resmi, Cepat & Terpercaya"
    for dx, dy in [(-1, -1), (1, 1), (-1, 1), (1, -1), (0, 2), (2, 2)]:
        draw.text((CONTENT_X + dx, cur_y + dy), t_line2, fill=(2, 8, 24, 180), font=font_bold)
    draw.text((CONTENT_X, cur_y), t_line2, fill=(255, 255, 255, 255), font=font_bold)
    cur_y += 75
    
    # (C) Vertical Dashed Divider Line & Editorial Description (Like Image 1)
    divider_x = CONTENT_X + 4
    divider_y0 = cur_y - 4
    divider_y1 = cur_y + 92
    
    # Draw dashed vertical line
    for dy in range(divider_y0, divider_y1, 8):
        draw.line([(divider_x, dy), (divider_x, dy + 4)], fill=(0, 229, 255, 210), width=2)
        
    sub_x = divider_x + 22
    sub_line1 = "Platform e-ticketing konser musik, festival, dan seminar akbar."
    sub_line2 = "E-ticket QR Code instan, verifikasi panitia cepat tanpa antre."
    sub_line3 = "Proteksi pembayaran 100% aman & jaminan tiket resmi terverifikasi."
    
    # Text shadow for subtext
    for dx, dy in [(0, 1), (1, 1)]:
        draw.text((sub_x + dx, cur_y + dy), sub_line1, fill=(2, 8, 24, 160), font=font_subhead)
        draw.text((sub_x + dx, cur_y + 30 + dy), sub_line2, fill=(2, 8, 24, 160), font=font_subhead)
        draw.text((sub_x + dx, cur_y + 60 + dy), sub_line3, fill=(2, 8, 24, 160), font=font_subhead)
        
    draw.text((sub_x, cur_y), sub_line1, fill=(241, 245, 249, 255), font=font_subhead)
    draw.text((sub_x, cur_y + 30), sub_line2, fill=(203, 213, 225, 240), font=font_subhead)
    draw.text((sub_x, cur_y + 60), sub_line3, fill=(148, 163, 184, 220), font=font_subhead)
    
    # Merge text layer
    canvas = Image.alpha_composite(canvas, txt_layer)
    final_rgb = canvas.convert('RGB')
    
    # 4. Save to all required banner destinations
    dest_dirs = [
        r'c:\laragon\www\FunrunHore\public\images\banners',
        r'c:\laragon\www\FunrunHore\public\storage\banners',
        r'c:\laragon\www\FunrunHore\storage\app\public\banners',
    ]
    for d in dest_dirs:
        os.makedirs(d, exist_ok=True)
        p = os.path.join(d, 'setiket-hero-promo.webp')
        final_rgb.save(p, 'WEBP', quality=95, method=6)
        
    artifact_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_lowpoly_banner.webp'
    final_rgb.save(artifact_path, 'WEBP', quality=95, method=6)
    
    # Also save as main artifact setiket_hero_promo_banner.webp
    main_art = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_hero_promo_banner.webp'
    final_rgb.save(main_art, 'WEBP', quality=95, method=6)
    
    print("SUCCESS: Generated low-poly vector banner at", artifact_path)

if __name__ == '__main__':
    create_banner()
