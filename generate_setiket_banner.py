import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

def create_banner():
    # Target dimensions: 1920 x 720 px (Recommended Hero Banner specifications)
    WIDTH, HEIGHT = 1920, 720
    
    # 1. Create base canvas with deep obsidian/navy background
    # Dark luxury palette: #070e17 to #0b1523
    canvas = Image.new('RGBA', (WIDTH, HEIGHT), (7, 14, 23, 255))
    
    # Draw soft ambient radial glow behind left 3D element and subtle top-right ambient
    glow_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    glow_draw = ImageDraw.Draw(glow_layer)
    
    # Left cyan/blue ambient glow
    for r in range(480, 50, -15):
        alpha = int(35 * (1 - r / 480))
        glow_draw.ellipse(
            (250 - r, 360 - r, 250 + r, 360 + r),
            fill=(0, 80, 255, alpha)
        )
    for r in range(300, 30, -10):
        alpha = int(50 * (1 - r / 300))
        glow_draw.ellipse(
            (320 - r, 280 - r, 320 + r, 280 + r),
            fill=(0, 192, 250, alpha)
        )
        
    # Top-right very soft blue ambient
    for r in range(500, 100, -20):
        alpha = int(18 * (1 - r / 500))
        glow_draw.ellipse(
            (1700 - r, 100 - r, 1700 + r, 100 + r),
            fill=(0, 80, 255, alpha)
        )
        
    glow_layer = glow_layer.filter(ImageFilter.GaussianBlur(40))
    canvas = Image.alpha_composite(canvas, glow_layer)
    
    # 2. Process and composite the 3D translucent glass ticket
    base_render_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_hero_banner_1792356303310.jpg'
    if os.path.exists(base_render_path):
        base_3d = Image.open(base_render_path).convert('RGBA')
        b_w, b_h = base_3d.size
        
        # Scale to match height 720
        scale = HEIGHT / b_h
        new_w = int(b_w * scale)
        new_h = HEIGHT
        base_3d_resized = base_3d.resize((new_w, new_h), Image.Resampling.LANCZOS)
        
        # Create horizontal gradient mask to smoothly fade into background on the right
        mask = Image.new('L', (new_w, new_h), 255)
        fade_start = int(new_w * 0.50)
        fade_end = int(new_w * 0.86)
        for x in range(fade_start, new_w):
            if x >= fade_end:
                alpha = 0
            else:
                progress = (x - fade_start) / (fade_end - fade_start)
                alpha = int(255 * (0.5 + 0.5 * math.cos(progress * math.pi)))
            for y in range(new_h):
                mask.putpixel((x, y), alpha)
                
        base_3d_resized.putalpha(mask)
        canvas.paste(base_3d_resized, (0, 0), base_3d_resized)
        
    # 3. Accents Layer for text & UI elements
    accents_layer = Image.new('RGBA', (WIDTH, HEIGHT), (0, 0, 0, 0))
    acc_draw = ImageDraw.Draw(accents_layer)
    
    # 4. Prepare Typography and Fonts
    font_bold_path = r'C:\Windows\Fonts\segoeuib.ttf'
    font_sb_path = r'C:\Windows\Fonts\seguisb.ttf'
    font_reg_path = r'C:\Windows\Fonts\segoeui.ttf'
    
    if not os.path.exists(font_bold_path):
        font_bold_path = r'C:\Windows\Fonts\arialbd.ttf'
        font_sb_path = r'C:\Windows\Fonts\arialbd.ttf'
        font_reg_path = r'C:\Windows\Fonts\arial.ttf'
        
    font_tag = ImageFont.truetype(font_sb_path, 13)
    font_title = ImageFont.truetype(font_bold_path, 52)
    font_desc = ImageFont.truetype(font_reg_path, 19)
    font_badge = ImageFont.truetype(font_sb_path, 13)
    
    # 5. Right Content Placement (X starts at 840, room up to 1860)
    CONTENT_X = 840
    current_y = 100
    
    # (A) SeTiket Official Logo
    logo_path = r'c:\laragon\www\FunrunHore\public\images\setiketbg.webp'
    if os.path.exists(logo_path):
        logo_img = Image.open(logo_path).convert('RGBA')
        orig_lw, orig_lh = logo_img.size
        target_lh = 80
        target_lw = int(orig_lw * (target_lh / orig_lh))
        logo_resized = logo_img.resize((target_lw, target_lh), Image.Resampling.LANCZOS)
        
        # Add subtle soft drop shadow under logo
        shadow_logo = Image.new('RGBA', (target_lw + 24, target_lh + 24), (0, 0, 0, 0))
        shadow_logo.paste(logo_resized, (12, 12))
        shadow_mask = shadow_logo.split()[3]
        black_shadow = Image.new('RGBA', shadow_logo.size, (0, 0, 0, 140))
        black_shadow.putalpha(shadow_mask)
        black_shadow = black_shadow.filter(ImageFilter.GaussianBlur(8))
        
        canvas.paste(black_shadow, (CONTENT_X - 12, current_y - 8), black_shadow)
        canvas.paste(logo_resized, (CONTENT_X, current_y), logo_resized)
        
        # Tag pill next to logo: glowing dot + "PLATFORM RESMI TIKET & KONSER"
        tag_x = CONTENT_X + target_lw + 24
        tag_y = current_y + 22
        tag_text = "PLATFORM RESMI TIKET & KONSER"
        tag_bbox = acc_draw.textbbox((0, 0), tag_text, font=font_tag)
        tag_text_w = tag_bbox[2] - tag_bbox[0]
        tag_w = tag_text_w + 44
        tag_h = 34
        
        # Draw frosted pill for tag
        acc_draw.rounded_rectangle(
            (tag_x, tag_y, tag_x + tag_w, tag_y + tag_h),
            radius=17,
            fill=(0, 80, 255, 30),
            outline=(0, 192, 250, 110),
            width=1
        )
        # Glowing cyan dot
        dot_x = tag_x + 16
        dot_y = tag_y + 13
        acc_draw.ellipse((dot_x - 3, dot_y - 3, dot_x + 5, dot_y + 5), fill=(0, 192, 250, 255))
        acc_draw.text((tag_x + 28, tag_y + 8), tag_text, fill=(56, 189, 248, 255), font=font_tag)
        
        current_y += target_lh + 36
    else:
        current_y += 30

    # (B) Main Headline (Crisp, High Impact Editorial like Sebaris)
    # Line 1: "Pesan Tiket Konser & Event" (Pure White)
    # Line 2: "Lebih Cepat, Resmi & Terpercaya" (Vibrant Electric Cyan)
    line1 = "Pesan Tiket Konser & Event"
    line2_prefix = "Lebih Cepat, "
    line2_highlight = "Resmi & Terpercaya"
    
    # Draw Line 1
    acc_draw.text((CONTENT_X, current_y), line1, fill=(255, 255, 255, 255), font=font_title)
    current_y += 66
    
    # Draw Line 2
    l2_p_bbox = acc_draw.textbbox((0, 0), line2_prefix, font=font_title)
    l2_p_w = l2_p_bbox[2] - l2_p_bbox[0]
    
    acc_draw.text((CONTENT_X, current_y), line2_prefix, fill=(255, 255, 255, 255), font=font_title)
    
    # Draw highlighted cyan text with subtle glow
    cyan_glow = (0, 192, 250, 255)
    for dx, dy in [(-1, 0), (1, 0), (0, -1), (0, 1)]:
        acc_draw.text((CONTENT_X + l2_p_w + dx, current_y + dy), line2_highlight, fill=(0, 192, 250, 80), font=font_title)
    acc_draw.text((CONTENT_X + l2_p_w, current_y), line2_highlight, fill=cyan_glow, font=font_title)
    
    current_y += 74
    
    # (C) Subheadline Paragraph (2 Lines, Clean & Elegant)
    sub1 = "Platform e-ticketing modern untuk festival musik, konser akbar, olahraga fun run, dan seminar."
    sub2 = "E-ticket QR Code instan, verifikasi panitia cepat tanpa antre, dan proteksi pembayaran 100% aman."
    
    acc_draw.text((CONTENT_X, current_y), sub1, fill=(148, 163, 184, 255), font=font_desc)
    current_y += 28
    acc_draw.text((CONTENT_X, current_y), sub2, fill=(148, 163, 184, 255), font=font_desc)
    current_y += 44
    
    # (D) Three Feature Trust Badges (Like Sebaris / Apple Keynote Pills)
    badges = [
        ("E-Ticket QR Code Instan", (0, 192, 250)),
        ("Check-In Scanner Cepat", (16, 185, 129)),
        ("100% Tiket Terverifikasi", (245, 158, 11))
    ]
    
    badge_x = CONTENT_X
    badge_h = 36
    for b_text, dot_color in badges:
        b_bbox = acc_draw.textbbox((0, 0), b_text, font=font_badge)
        text_w = b_bbox[2] - b_bbox[0]
        b_w = text_w + 48
        
        # Frosted glass background
        acc_draw.rounded_rectangle(
            (badge_x, current_y, badge_x + b_w, current_y + badge_h),
            radius=18,
            fill=(255, 255, 255, 14),
            outline=(255, 255, 255, 36),
            width=1
        )
        
        # Colored dot
        dot_bx = badge_x + 16
        dot_by = current_y + 14
        acc_draw.ellipse((dot_bx - 3, dot_by - 3, dot_bx + 4, dot_by + 4), fill=(*dot_color, 255))
        
        # Text
        acc_draw.text((badge_x + 28, current_y + 9), b_text, fill=(241, 245, 249, 255), font=font_badge)
        badge_x += b_w + 14

    # Merge accents into canvas
    canvas = Image.alpha_composite(canvas, accents_layer)
    
    # 6. Save in required formats & locations
    dest_dir_images = r'c:\laragon\www\FunrunHore\public\images\banners'
    dest_dir_storage = r'c:\laragon\www\FunrunHore\public\storage\banners'
    dest_dir_app = r'c:\laragon\www\FunrunHore\storage\app\public\banners'
    
    for d in [dest_dir_images, dest_dir_storage, dest_dir_app]:
        os.makedirs(d, exist_ok=True)
        
    final_rgb = canvas.convert('RGB')
    
    webp_path_images = os.path.join(dest_dir_images, 'setiket-hero-promo.webp')
    webp_path_storage = os.path.join(dest_dir_storage, 'setiket-hero-promo.webp')
    webp_path_app = os.path.join(dest_dir_app, 'setiket-hero-promo.webp')
    
    final_rgb.save(webp_path_images, 'WEBP', quality=95, method=6)
    final_rgb.save(webp_path_storage, 'WEBP', quality=95, method=6)
    final_rgb.save(webp_path_app, 'WEBP', quality=95, method=6)
    
    # Also save in brain artifacts directory
    artifact_path = r'C:\Users\yogafatwanto\.gemini\antigravity-ide\brain\f7b223ac-8d37-4d7c-94a5-fc5df9e1e333\setiket_hero_promo_banner.webp'
    final_rgb.save(artifact_path, 'WEBP', quality=95, method=6)
    
    print(f"SUCCESS: Created banner at {webp_path_images}")
    print(f"Dimensions: {WIDTH}x{HEIGHT}")
    file_size_kb = os.path.getsize(webp_path_images) / 1024
    print(f"File Size: {file_size_kb:.1f} KB")

if __name__ == '__main__':
    create_banner()
