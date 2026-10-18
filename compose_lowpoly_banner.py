import os
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_banner():
    WIDTH, HEIGHT = 1920, 720
    
    # 1. Load generated white low-poly background with SeTiket colors
    bg_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_white_lowpoly_1792361286956.jpg'
    img = Image.open(bg_path).convert('RGB')
    
    # Crop the banner card area (excluding grey studio shadow)
    crop_box = (68, 72, 1306, 694)
    banner_cropped = img.crop(crop_box)
    
    # Resize to exact 1920 x 720 px
    banner_base = banner_cropped.resize((WIDTH, HEIGHT), Image.Resampling.LANCZOS)
    canvas = banner_base.convert('RGBA')
    
    # 2. Typography Layer (MATCHING REFERENCE IMAGE 1 - WHITE THEME, NO PILLS, NO AI SLOP)
    txt_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    draw = ImageDraw.Draw(txt_layer)
    
    # Load fonts
    font_bold = ImageFont.truetype(r"C:\Windows\Fonts\segoeuib.ttf", 48)
    font_script = ImageFont.truetype(r"C:\Windows\Fonts\BRUSHSCI.TTF", 68)
    font_subhead = ImageFont.truetype(r"C:\Windows\Fonts\segoeui.ttf", 20)
    
    CONTENT_X = 1000
    cur_y = 150
    
    # (A) Official SeTiket Ticket Logo on White Background
    logo_path = r'c:\laragon\www\FunrunHore\public\images\setiketbg.webp'
    if os.path.exists(logo_path):
        logo_img = Image.open(logo_path).convert('RGBA')
        lw, lh = logo_img.size
        target_lw = 180
        target_lh = int(lh * (target_lw / lw))
        logo_resized = logo_img.resize((target_lw, target_lh), Image.Resampling.LANCZOS)
        
        # Soft subtle shadow behind logo for contrast on white
        shadow_logo = Image.new('RGBA', (target_lw + 20, target_lh + 20), (0, 0, 0, 0))
        shadow_logo.paste(logo_resized, (10, 10))
        shadow_mask = shadow_logo.split()[3]
        black_shadow = Image.new('RGBA', shadow_logo.size, (0, 30, 100, 60))
        black_shadow.putalpha(shadow_mask)
        black_shadow = black_shadow.filter(ImageFilter.GaussianBlur(5))
        
        canvas.paste(black_shadow, (CONTENT_X - 10, cur_y - 10), black_shadow)
        canvas.paste(logo_resized, (CONTENT_X, cur_y), logo_resized)
        
        cur_y += target_lh + 30
    else:
        cur_y += 30
        
    # (B) Main Headline (Bold, High Contrast, Human Graphic Design on White)
    # Line 1: 'Pesan Tiket' in deep slate (#0f172a) + 'Konser & Event' in vibrant SeTiket blue (#0066ff) script
    t_pesan = "Pesan Tiket "
    w_pesan = draw.textbbox((0, 0), t_pesan, font=font_bold)[2]
    
    draw.text((CONTENT_X, cur_y), t_pesan, fill=(15, 23, 42), font=font_bold)
    draw.text((CONTENT_X + w_pesan, cur_y - 12), "Konser & Event", fill=(0, 102, 255), font=font_script)
    cur_y += 62
    
    # Line 2: 'Resmi, Cepat & Terpercaya'
    t_line2 = "Resmi, Cepat & Terpercaya"
    draw.text((CONTENT_X, cur_y), t_line2, fill=(30, 41, 59), font=font_bold)
    cur_y += 75
    
    # (C) Vertical Dashed Divider Line & Editorial Description (Like Image 1)
    divider_x = CONTENT_X + 4
    divider_y0 = cur_y - 4
    divider_y1 = cur_y + 92
    
    # Draw dashed vertical line in SeTiket blue (#0066ff)
    for dy in range(divider_y0, divider_y1, 8):
        draw.line([(divider_x, dy), (divider_x, dy + 4)], fill=(0, 102, 255), width=2)
        
    sub_x = divider_x + 22
    sub_line1 = "Platform e-ticketing konser musik, festival, dan seminar akbar."
    sub_line2 = "E-ticket QR Code instan, verifikasi panitia cepat tanpa antre."
    sub_line3 = "Proteksi pembayaran 100% aman & jaminan tiket resmi terverifikasi."
    
    draw.text((sub_x, cur_y), sub_line1, fill=(30, 41, 59), font=font_subhead)
    draw.text((sub_x, cur_y + 30), sub_line2, fill=(51, 65, 85), font=font_subhead)
    draw.text((sub_x, cur_y + 60), sub_line3, fill=(71, 85, 105), font=font_subhead)
    
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
        final_rgb.save(os.path.join(d, 'setiket-hero-promo.webp'), 'WEBP', quality=95, method=6)
        final_rgb.save(os.path.join(d, 'setiket-lowpoly-promo.webp'), 'WEBP', quality=95, method=6)
        
    artifact_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_white_lowpoly_banner.webp'
    final_rgb.save(artifact_path, 'WEBP', quality=95, method=6)
    
    # Also save as setiket_lowpoly_banner.webp
    art2 = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_lowpoly_banner.webp'
    final_rgb.save(art2, 'WEBP', quality=95, method=6)
    
    print("SUCCESS: Generated white low-poly SeTiket banner at", artifact_path)

if __name__ == '__main__':
    create_banner()
