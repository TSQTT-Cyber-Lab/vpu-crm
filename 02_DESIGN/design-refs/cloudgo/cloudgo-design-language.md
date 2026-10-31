# Design Language: CloudGO - Giải pháp CRM & AI chuyên sâu theo ngành hàng đầu Việt Nam

> Extracted from `https://cloudgo.vn/` on October 31, 2026
> 3730 elements analyzed

This document describes the complete design language of the website. It is structured for AI/LLM consumption — use it to faithfully recreate the visual design in any framework.

## Color Palette

### Primary Colors

| Role | Hex | RGB | HSL | Usage Count |
|------|-----|-----|-----|-------------|
| Primary | `#1b74e4` | rgb(27, 116, 228) | hsl(213, 79%, 50%) | 525 |
| Secondary | `#ffd700` | rgb(255, 215, 0) | hsl(51, 100%, 50%) | 20 |
| Accent | `#cb2134` | rgb(203, 33, 52) | hsl(353, 72%, 46%) | 10 |

### Tonal Ramps

Generated in OKLCH from each role colour, so the steps are perceptually even. The site's own colour keeps its position in the ladder.

| Role | 50 | 100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900 | 950 |
|------|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|
| primary | `#c5f9ff` | `#b4edff` | `#9ddbff` | `#7ac1ff` | `#4e9fff` | **`#1b74e4`** | `#1168d1` | `#0052b0` | `#004295` | `#00347d` | `#001b4d` |
| secondary | `#fff69f` | `#ffea89` | **`#ffd700`** | `#dfbe2c` | `#c29c00` | `#a47d00` | `#8c6700` | `#735200` | `#5f4200` | `#4e3400` | `#2d1b00` |
| accent | `#ffd5d0` | `#ffc6c1` | `#ffafab` | `#ff8e8c` | `#ff6366` | `#e13c46` | **`#cb2134`** | `#a30c23` | `#890019` | `#720010` | `#450004` |
| neutral | `#f5f5f5` | `#eaeaea` | **`#d9d9d9`** | `#c0c0c0` | `#9f9f9f` | `#828282` | `#6c6c6c` | `#575757` | `#464646` | `#393939` | `#1f1f1f` |

### Semantic Pairs

| Role | Background | Foreground | Contrast | Level |
|------|------------|------------|----------|-------|
| `surface.page` | `#ffffff` | `#000000` | 21:1 | AAA |
| `surface.raised` | `#e5f1ff` | `#000000` | 18.36:1 | AAA |
| `action.primary` | `#1b74e4` | `#000000` | 4.67:1 | AA |
| `action.secondary` | `#ffd700` | `#000000` | 14.97:1 | AAA |
| `action.accent` | `#cb2134` | `#ffffff` | 5.52:1 | AA |

### Dominance

Share of painted background area — what the page looks like, as opposed to what it declares most often.

- `#ffffff` — 75%
- `#e1eeff` — 6%
- `#1b74e4` — 5%
- `#ebfffa` — 5%
- `#cee4ff` — 4%
- `#fdecea` — 4%

### Neutral Colors

| Hex | HSL | Usage Count |
|-----|-----|-------------|
| `#ffffff` | hsl(0, 0%, 100%) | 2677 |
| `#d9d9d9` | hsl(0, 0%, 85%) | 323 |
| `#6b6e71` | hsl(210, 3%, 43%) | 202 |
| `#000000` | hsl(0, 0%, 0%) | 156 |
| `#4a4a4a` | hsl(0, 0%, 29%) | 143 |
| `#777777` | hsl(0, 0%, 47%) | 88 |
| `#222222` | hsl(0, 0%, 13%) | 68 |
| `#e3e6eb` | hsl(217, 17%, 91%) | 59 |
| `#545b64` | hsl(214, 9%, 36%) | 32 |
| `#b9b9b9` | hsl(0, 0%, 73%) | 16 |
| `#919395` | hsl(210, 2%, 58%) | 11 |
| `#0c0c0c` | hsl(0, 0%, 5%) | 11 |

### Background Colors

Used on large-area elements: `#ffffff`, `#e5f1ff`, `#cee4ff`, `#1b74e4`

### Text Colors

Text color palette: `#273444`, `#000000`, `#ffffff`, `#1b74e4`, `#808080`, `#4a4a4a`, `#08278b`

### Gradients

```css
background-image: linear-gradient(rgb(27, 116, 228) 0%, rgb(0, 178, 227) 100%);
```

```css
background-image: linear-gradient(to right, rgb(1, 97, 249), rgb(17, 211, 235));
```

```css
background-image: linear-gradient(188deg, rgb(27, 116, 228) 0%, rgb(73, 213, 255) 91.76%);
```

```css
background-image: linear-gradient(135deg, rgb(1, 97, 249), rgb(0, 126, 251), rgb(17, 211, 235));
```

```css
background-image: linear-gradient(135deg, rgb(2, 100, 250), rgb(16, 207, 236));
```

```css
background-image: linear-gradient(to right, rgb(27, 116, 228), rgb(16, 213, 235));
```

```css
background-image: linear-gradient(135deg, rgb(33, 109, 221) 0%, rgb(66, 133, 244) 100%);
```

```css
background-image: linear-gradient(to top, rgba(0, 0, 0, 0.9), rgba(0, 0, 0, 0));
```

```css
background-image: linear-gradient(135deg, rgb(66, 133, 244), rgb(27, 116, 228));
```

```css
background-image: linear-gradient(90deg, rgb(1, 97, 249) 0%, rgb(0, 126, 251) 47.5%, rgb(17, 211, 235) 100%);
```

```css
background-image: linear-gradient(160deg, rgb(27, 116, 228), rgb(13, 87, 184));
```

### Full Color Inventory

| Hex | Contexts | Count |
|-----|----------|-------|
| `#273444` | text, border | 3533 |
| `#ffffff` | background, text, border | 2677 |
| `#1b74e4` | background, border, text | 525 |
| `#d9d9d9` | border, background | 323 |
| `#6b6e71` | text, border | 202 |
| `#000000` | text, border | 156 |
| `#4a4a4a` | text, border | 143 |
| `#e5372e` | text, border | 98 |
| `#222222` | text, border | 68 |
| `#545b64` | text, border | 32 |
| `#ffd700` | text, border | 20 |
| `#529fff` | border | 18 |
| `#b9b9b9` | border | 16 |
| `#cb2134` | background | 10 |
| `#e1eeff` | background | 8 |
| `#1a7f37` | text, border | 12 |
| `#919395` | background, text, border | 11 |
| `#0c0c0c` | text | 11 |
| `#ffefe2` | background | 4 |
| `#33404f` | text, border | 6 |
| `#c9f7f0` | background | 4 |
| `#0b5ed7` | text, border | 4 |
| `#808080` | text | 2 |
| `#08278b` | text, border | 2 |
| `#06162d` | background | 2 |
| `#4a90e2` | background | 1 |

## Typography

### Font Families

- **Roboto** — used for all (592 elements)
- **Inter** — used for all (71 elements)

### Type System

- **Scale ratio** — 1.067 (minor second), anchored at 8px · 100% of sizes sit on the ladder
- **Measure** — ~114 characters per line (wide)
- **Fluid type** — none; sizes are fixed per breakpoint

### Named Scale

| Role | Size | Weight | Line Height | Tracking |
|------|------|--------|-------------|----------|
| `h1` | 42px / 2.625rem | 800 | 54.6px | normal |
| `h2` | 38px / 2.375rem | 500 | 49.4px | normal |
| `title-1` | 36px / 2.25rem | 100 | 44px | normal |
| `title-2` | 35px / 2.1875rem | 900 | 35px | normal |
| `title-3` | 34px / 2.125rem | 700 | normal | normal |
| `title-4` | 32px / 2rem | 500 | 41.6px | normal |
| `title-5` | 30px / 1.875rem | 900 | 30px | normal |
| `title-6` | 28.8px / 1.8rem | 700 | 46.08px | normal |
| `h3` | 28px / 1.75rem | 700 | 44.8px | normal |
| `title-7` | 26px / 1.625rem | 700 | 36.4px | 0.5px |
| `title-8` | 24px / 1.5rem | 700 | 28.8px | normal |
| `title-9` | 23px / 1.4375rem | 700 | 26.45px | normal |
| `title-10` | 22px / 1.375rem | 700 | 35.2px | normal |
| `title-11` | 21px / 1.3125rem | 400 | 35.7px | normal |
| `h4` | 20px / 1.25rem | 700 | 32px | normal |

### Type Scale

| Size (px) | Size (rem) | Weight | Line Height | Letter Spacing | Used On |
|-----------|------------|--------|-------------|----------------|---------|
| 42px | 2.625rem | 800 | 54.6px | normal | h1, br |
| 38px | 2.375rem | 500 | 49.4px | normal | h2 |
| 36px | 2.25rem | 100 | 44px | normal | div |
| 35px | 2.1875rem | 900 | 35px | normal | i |
| 34px | 2.125rem | 700 | normal | normal | p |
| 32px | 2rem | 500 | 41.6px | normal | h2, span, i |
| 30px | 1.875rem | 900 | 30px | normal | i, span |
| 28.8px | 1.8rem | 700 | 46.08px | normal | strong |
| 28px | 1.75rem | 700 | 44.8px | normal | p, h3 |
| 26px | 1.625rem | 700 | 36.4px | 0.5px | h3 |
| 24px | 1.5rem | 700 | 28.8px | normal | div, br, span, i |
| 23px | 1.4375rem | 700 | 26.45px | normal | p |
| 22px | 1.375rem | 700 | 35.2px | normal | p, button |
| 21px | 1.3125rem | 400 | 35.7px | normal | p |
| 20px | 1.25rem | 700 | 32px | normal | a, div, img, button |

### Heading Scale

```css
h1 { font-size: 42px; font-weight: 800; line-height: 54.6px; }
h2 { font-size: 38px; font-weight: 500; line-height: 49.4px; }
h2 { font-size: 32px; font-weight: 500; line-height: 41.6px; }
h3 { font-size: 28px; font-weight: 700; line-height: 44.8px; }
h3 { font-size: 26px; font-weight: 700; line-height: 36.4px; }
h4 { font-size: 20px; font-weight: 700; line-height: 32px; }
h3 { font-size: 18px; font-weight: 700; line-height: 28.8px; }
h2 { font-size: 16px; font-weight: 400; line-height: 18.4px; }
h3 { font-size: 15px; font-weight: 700; line-height: 24px; }
```

### Body Text

```css
body { font-size: 16px; font-weight: 400; line-height: 18.4px; }
```

### Font Weights in Use

`400` (3027x), `700` (339x), `900` (186x), `500` (81x), `600` (44x), `300` (35x), `800` (15x), `100` (3x)

## Spacing

**Base unit:** 2px

| Token | Value | Rem |
|-------|-------|-----|
| spacing-1 | 1px | 0.0625rem |
| spacing-38 | 38px | 2.375rem |
| spacing-43 | 43px | 2.6875rem |
| spacing-48 | 48px | 3rem |
| spacing-60 | 60px | 3.75rem |
| spacing-65 | 65px | 4.0625rem |
| spacing-70 | 70px | 4.375rem |
| spacing-73 | 73px | 4.5625rem |
| spacing-80 | 80px | 5rem |
| spacing-83 | 83px | 5.1875rem |
| spacing-86 | 86px | 5.375rem |
| spacing-90 | 90px | 5.625rem |
| spacing-100 | 100px | 6.25rem |
| spacing-150 | 150px | 9.375rem |
| spacing-214 | 214px | 13.375rem |

## Border Radii

| Label | Value | Count |
|-------|-------|-------|
| sm | 3px | 6 |
| md | 6px | 38 |
| md | 10px | 27 |
| lg | 14px | 18 |
| xl | 20px | 12 |
| xl | 24px | 5 |
| full | 30px | 13 |
| full | 33px | 1 |
| full | 48px | 1 |
| full | 64px | 3 |
| full | 100px | 20 |

## Elevation

6 levels from 15 raw shadows · neutral tint

**xs** — blur 4px, y 1px, used 2×
```css
box-shadow: rgba(102, 98, 98, 0.4) 0px 1px 4px 0px;
```

**sm** — blur 8px, y 0px, used 18× · tinted (hue 213°)
```css
box-shadow: rgb(82, 159, 255) 0px 0px 8px 0px;
```

**md** — blur 16px, y 0px, used 19× · tinted (hue 213°)
```css
box-shadow: rgba(27, 116, 228, 0.15) 0px 0px 16px 0px;
```

**lg** — blur 20px, y 4px, used 3×
```css
box-shadow: rgba(0, 0, 0, 0.3) 0px 4px 20px 0px;
```

**xl** — blur 30px, y 10px, used 9×
```css
box-shadow: rgba(0, 0, 0, 0.2) 0px 10px 30px 0px;
```

**2xl** — blur 70px, y 24px, used 2×
```css
box-shadow: rgba(0, 0, 0, 0.35) 0px 24px 70px 0px;
```

## CSS Custom Properties

### Colors

```css
--primary: #1b74e4;
--secondary: #676767;
--primary-highlight: #fff5f6;
```

### Spacing

```css
--font-family-monospace: SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;
```

### Typography

```css
--font-family-sans-serif: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
--fa-font-light: normal 300 1em/1 "Font Awesome 6 Pro";
--fa-font-regular: normal 400 1em/1 "Font Awesome 6 Pro";
--fa-font-duotone: normal 900 1em/1 "Font Awesome 6 Duotone";
--fa-font-thin: normal 100 1em/1 "Font Awesome 6 Pro";
--fa-font-brands: normal 400 1em/1 "Font Awesome 6 Brands";
--fa-font-solid: normal 900 1em/1 "Font Awesome 6 Pro";
```

### Other

```css
--blue: #007bff;
--indigo: #6610f2;
--purple: #6f42c1;
--pink: #e83e8c;
--red: #dc3545;
--orange: #fd7e14;
--yellow: #ffc107;
--green: #28a745;
--teal: #20c997;
--cyan: #17a2b8;
--gray: #C0CCDA;
--gray-dark: #3C4858;
--success: #41D600;
--info: #057bc4;
--warning: #fccb54;
--danger: #C80017;
--light: #EFF2F7;
--dark: #273444;
--default: #dedede;
--white: #FFF;
--lighter: #f3f3f3;
--f1f5fb: #f1f5fb;
--breakpoint-xs: 0;
--breakpoint-sm: 576px;
--breakpoint-md: 768px;
--breakpoint-lg: 992px;
--breakpoint-xl: 1200px;
```

### Semantic

```css
--green: #28a745;
--success: #41D600;
--yellow: #ffc107;
--warning: #fccb54;
--red: #dc3545;
--danger: #C80017;
--blue: #007bff;
--info: #057bc4;
```

## Breakpoints

| Name | Value | Type |
|------|-------|------|
| xs | 360px | max-width |
| xs | 380px | max-width |
| 414px | 414px | max-width |
| sm | 450px | max-width |
| sm | 480px | max-width |
| sm | 576px | max-width |
| sm | 640px | max-width |
| sm | 641px | min-width |
| md | 767px | max-width |
| md | 768px | min-width |
| md | 769px | min-width |
| lg | 992px | max-width |
| lg | 1024px | max-width |
| 1199px | 1199px | max-width |
| 1200px | 1200px | max-width |
| 1400px | 1400px | max-width |

## Transitions & Animations

**Easing functions:** `ease`, `linear`

**Durations:** `0.3s`, `0.15s`, `0.1s`, `0.4s`, `0.5s`, `0.2s`, `5s`, `0.22s`, `0.18s`, `0.08s`

### Common Transitions

```css
transition: all;
transition: box-shadow 0.3s, background-color 0.3s;
transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
transition: 0.1s linear;
transition: 0.3s;
transition: transform 0.3s;
transition: opacity 0.3s;
transition: opacity 0.4s ease-in-out;
transition: 0.5s;
transition: opacity 0.2s;
```

### Keyframe Animations

**navbarSlideDown**
```css
@keyframes navbarSlideDown {
  0% { transform: translateY(-100%); }
  100% { transform: translateY(0px); }
}
```

**progress-bar-stripes**
```css
@keyframes progress-bar-stripes {
  0% { background-position: 1rem 0px; }
  100% { background-position: 0px 0px; }
}
```

**spinner-border**
```css
@keyframes spinner-border {
  100% { transform: rotate(360deg); }
}
```

**spinner-grow**
```css
@keyframes spinner-grow {
  0% { transform: scale(0); }
  50% { opacity: 1; }
}
```

**slideIn**
```css
@keyframes slideIn {
  0% { transform: translateY(1rem); opacity: 0; }
  100% { transform: translateY(0rem); opacity: 1; }
  0% { transform: translateY(1rem); opacity: 0; }
}
```

**slideIn**
```css
@keyframes slideIn {
  0% { opacity: 0; }
  100% { transform: translateY(0px); opacity: 1; }
  0% { transform: translateY(1rem); opacity: 0; }
}
```

**slideDown**
```css
@keyframes slideDown {
  0% { opacity: 0; transform: translateY(-10px); }
  100% { opacity: 1; transform: translateY(0px); }
}
```

**slideDown**
```css
@keyframes slideDown {
  0% { opacity: 0; transform: translateY(-10px); }
  100% { opacity: 1; transform: translateY(0px); }
}
```

**pulse**
```css
@keyframes pulse {
  0% { box-shadow: rgba(255, 107, 53, 0.4) 0px 4px 15px; }
  50% { box-shadow: rgba(255, 107, 53, 0.7) 0px 4px 25px; transform: scale(1.05); }
  100% { box-shadow: rgba(255, 107, 53, 0.4) 0px 4px 15px; }
}
```

**pulse**
```css
@keyframes pulse {
  0% { transform: scale(0.9); }
  50% { transform: scale(1.1); }
  100% { transform: scale(0.9); }
}
```

## Component Patterns

Detected UI component patterns and their most common styles:

### Buttons (38 instances)

```css
.buttons {
  background-color: rgb(27, 116, 228);
  color: rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
  padding-top: 0px;
  padding-right: 0px;
  border-radius: 0px;
}
```

### Cards (122 instances)

```css
.cards {
  background-color: rgb(255, 255, 255);
  border-radius: 15px;
  box-shadow: rgb(82, 159, 255) 0px 0px 8px 0px;
  padding-top: 0px;
  padding-right: 0px;
}
```

### Inputs (74 instances)

```css
.inputs {
  background-color: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  border-color: rgb(39, 52, 68);
  border-radius: 0px;
  font-size: 13px;
  padding-top: 0px;
  padding-right: 0px;
}
```

### Links (524 instances)

```css
.links {
  color: rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
}
```

### Navigation (101 instances)

```css
.navigation {
  background-color: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  padding-top: 0px;
  padding-bottom: 0px;
  padding-left: 0px;
  padding-right: 0px;
  position: static;
  box-shadow: rgba(0, 0, 0, 0.05) 0px 3px 7px 0px;
}
```

### Footer (25 instances)

```css
.footer {
  background-color: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  padding-top: 0px;
  padding-bottom: 0px;
  font-size: 16px;
}
```

### Modals (30 instances)

```css
.modals {
  background-color: rgb(255, 255, 255);
  border-radius: 0px;
  box-shadow: rgba(0, 0, 0, 0.35) 0px 24px 70px 0px;
  padding-top: 0px;
  padding-right: 0px;
  max-width: 1256px;
}
```

### Dropdowns (44 instances)

```css
.dropdowns {
  background-color: rgb(255, 255, 255);
  border-radius: 0px;
  box-shadow: rgba(0, 0, 0, 0.05) 0px 3px 7px 0px;
  border-color: rgb(39, 52, 68);
  padding-top: 0px;
}
```

### Badges (3 instances)

```css
.badges {
  color: rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
  padding-top: 0px;
  padding-right: 5px;
  border-radius: 0px;
}
```

### Tabs (6 instances)

```css
.tabs {
  color: rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
  padding-top: 10px;
  padding-right: 20px;
  border-color: rgb(39, 52, 68);
  border-radius: 0px;
}
```

### Accordions (17 instances)

```css
.accordions {
  background-color: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  font-size: 16px;
  padding-top: 0px;
  padding-right: 0px;
  border-color: rgb(39, 52, 68);
}
```

### ProgressBars (1 instances)

```css
.progressBars {
  color: rgb(39, 52, 68);
  border-radius: 0px;
  font-size: 16px;
}
```

### Switches (10 instances)

```css
.switches {
  background-color: rgb(27, 116, 228);
  border-radius: 0px;
  border-color: rgb(39, 52, 68);
}
```

## Component Clusters

Reusable component instances grouped by DOM structure and style similarity:

### Button — 8 instances, 1 variant

**Variant 1** (8 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 28px 0px 28px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 15px;
  font-weight: 700;
```

### Button — 6 instances, 3 variants

**Variant 1** (1 instance)

```css
  background: rgb(27, 116, 228);
  color: rgb(255, 255, 255);
  padding: 9.2px 12px 9.2px 12px;
  border-radius: 48px;
  border: 1px solid rgb(27, 116, 228);
  font-size: 16px;
  font-weight: 700;
```

**Variant 2** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 15px 30px 15px 30px;
  border-radius: 15px;
  border: 0px none rgb(255, 255, 255);
  font-size: 18px;
  font-weight: 700;
```

**Variant 3** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 10px 20px 10px 20px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 700;
```

### Input — 2 instances, 1 variant

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 10px 20px 10px 20px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 4 instances, 1 variant

**Variant 1** (4 instances)

```css
  background: rgb(225, 238, 255);
  color: rgb(39, 52, 68);
  padding: 18px 18px 18px 18px;
  border-radius: 10px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 2 instances, 1 variant

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 18 instances, 4 variants

**Variant 1** (1 instance)

```css
  background: rgb(255, 255, 255);
  color: rgb(255, 255, 255);
  padding: 20px 20px 20px 20px;
  border-radius: 16px;
  border: 1px solid rgb(27, 116, 228);
  font-size: 16px;
  font-weight: 400;
```

**Variant 2** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

**Variant 3** (12 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  padding: 20px 20px 20px 20px;
  border-radius: 16px;
  border: 1px solid rgb(185, 185, 185);
  font-size: 16px;
  font-weight: 400;
```

**Variant 4** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 17 instances, 1 variant

**Variant 1** (17 instances)

```css
  background: rgb(27, 116, 228);
  color: rgb(255, 255, 255);
  padding: 0px 0px 0px 0px;
  border-radius: 50px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

### Card — 13 instances, 3 variants

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

**Variant 2** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

**Variant 3** (8 instances)

```css
  background: rgb(255, 239, 226);
  color: rgb(39, 52, 68);
  padding: 20px 20px 20px 20px;
  border-radius: 15px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 18 instances, 1 variant

**Variant 1** (18 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  padding: 20px 20px 20px 20px;
  border-radius: 15px;
  border: 1px solid rgb(185, 185, 185);
  font-size: 16px;
  font-weight: 400;
```

### Card — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 4 instances, 1 variant

**Variant 1** (4 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(27, 116, 228);
  padding: 12px 20px 12px 0px;
  border-radius: 3px;
  border: 0px none rgb(27, 116, 228);
  font-size: 16px;
  font-weight: 400;
```

### Button — 7 instances, 2 variants

**Variant 1** (4 instances)

```css
  background: rgb(27, 116, 228);
  color: rgb(255, 255, 255);
  padding: 8px 20px 8px 20px;
  border-radius: 35px;
  border: 0px none rgb(255, 255, 255);
  font-size: 12px;
  font-weight: 500;
```

**Variant 2** (3 instances)

```css
  background: rgba(255, 255, 255, 0.9);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 50%;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Button — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Button — 4 instances, 2 variants

**Variant 1** (2 instances)

```css
  background: rgba(255, 255, 255, 0.95);
  color: rgb(27, 116, 228);
  padding: 0px 0px 0px 0px;
  border-radius: 50%;
  border: 0px none rgb(27, 116, 228);
  font-size: 16px;
  font-weight: 400;
```

**Variant 2** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 0px 0px 0px 0px;
  border-radius: 50%;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

### Button — 6 instances, 1 variant

**Variant 1** (6 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Card — 3 instances, 1 variant

**Variant 1** (3 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(39, 52, 68);
  padding: 0px 0px 0px 0px;
  border-radius: 15px;
  border: 0px none rgb(39, 52, 68);
  font-size: 16px;
  font-weight: 400;
```

### Button — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 12px 30px 12px 30px;
  border-radius: 0px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

## Layout System

**33 grid containers** and **687 flex containers** detected.

### The System

- **Content column** — 1200px wide, 15px gutters (1% edge density)
  - Width ladder: 494px · 584px · 812px · 1000px · 1200px
- **Column system** — 4 columns
- **Section rhythm** — 40px vertical padding (ladder: 20px · 30px · 40px · 60px · 80px)
- **Gap scale** — 1px · 4px · 8px · 10px · 12px · 15px · 20px · 27px
- **Fluid layout** — 4 clamp/viewport declarations

```css
.modal-backdrop { width: 100vw; }
.vw-100 { width: 100vw; }
.navbar .dropdown-full { width: 100vw; }
.lp-popup__dialog { max-width: calc(-24px + 100vw); }
```

### Container Widths

| Max Width | Padding |
|-----------|---------|
| 1320px | 15px |
| 100% | 0px |
| 1140px | 15px |
| 1200px | 15px |
| 1125px | 0px |
| 1512px | 20px |
| 584px | 0px |
| 1470px | 40px |
| 494px | 0px |
| 605px | 0px |

### Grid Column Patterns

| Columns | Usage Count |
|---------|-------------|
| 2-column | 23x |
| 4-column | 6x |
| 1-column | 2x |
| 6-column | 1x |
| 3-column | 1x |

### Grid Templates

```css
grid-template-columns: 1160px;
gap: 30px;
grid-template-columns: 540px 540px;
gap: 80px;
grid-template-columns: 257.5px 257.5px 257.5px 257.5px;
gap: 20px;
grid-template-columns: 1080px;
gap: 15px;
grid-template-columns: 626px 474px;
gap: 60px;
```

### Flex Patterns

| Direction/Wrap | Count |
|----------------|-------|
| row/nowrap | 589x |
| row/wrap | 9x |
| column/nowrap | 89x |

**Gap values:** `1px`, `4px`, `5px`, `6px`, `6.759px`, `8px`, `10px 12px`, `10px`, `12px 16px`, `12px`, `15px`, `16px`, `20px`, `24px`, `27px`, `28px`, `30px`, `60px`, `80px`

## Accessibility (WCAG 2.1)

**Overall Score: 100%** — 18 passing, 0 failing color pairs

### Passing Color Pairs

| Foreground | Background | Ratio | Level |
|------------|------------|-------|-------|
| `#ffffff` | `#cb2134` | 5.52:1 | AA |
| `#ffffff` | `#1b74e4` | 4.5:1 | AA |
| `#1b74e4` | `#ffffff` | 4.5:1 | AAA |
| `#33404f` | `#eef2f7` | 9.4:1 | AAA |

## Design System Score

**Overall: 79/100 (Grade: C)**

| Category | Score |
|----------|-------|
| Color Discipline | 80/100 |
| Typography Consistency | 75/100 |
| Spacing System | 85/100 |
| Shadow Consistency | 78/100 |
| Border Radius Consistency | 65/100 |
| Accessibility | 100/100 |
| CSS Tokenization | 100/100 |

**Strengths:** Well-defined spacing scale, Strong accessibility compliance, Good CSS variable tokenization

**Issues:**
- 8 font weights in use — consider standardizing to 3 (regular, medium, bold)
- 29 distinct font sizes — consider a tighter type scale
- 695 !important rules — prefer specificity over overrides
- 91% of CSS is unused — consider purging
- 5392 duplicate CSS declarations

## Gradients

**11 unique gradients** detected.

| Type | Direction | Stops | Classification |
|------|-----------|-------|----------------|
| linear | — | 2 | brand |
| linear | to right | 2 | brand |
| linear | 188deg | 2 | brand |
| linear | 135deg | 3 | bold |
| linear | 135deg | 2 | brand |
| linear | to right | 2 | brand |
| linear | 135deg | 2 | brand |
| linear | to top | 2 | brand |
| linear | 135deg | 2 | brand |
| linear | 90deg | 3 | bold |
| linear | 160deg | 2 | brand |

```css
background: linear-gradient(rgb(27, 116, 228) 0%, rgb(0, 178, 227) 100%);
background: linear-gradient(to right, rgb(1, 97, 249), rgb(17, 211, 235));
background: linear-gradient(188deg, rgb(27, 116, 228) 0%, rgb(73, 213, 255) 91.76%);
background: linear-gradient(135deg, rgb(1, 97, 249), rgb(0, 126, 251), rgb(17, 211, 235));
background: linear-gradient(135deg, rgb(2, 100, 250), rgb(16, 207, 236));
```

## Z-Index Map

**15 unique z-index values** across 3 layers.

| Layer | Range | Elements |
|-------|-------|----------|
| modal | 1000,200000 | div.d.r.o.p.d.o.w.n.-.m.e.n.u. .m.e.g.a.-.m.e.n.u, div.d.r.o.p.d.o.w.n.-.m.e.n.u. .m.e.g.a.-.m.e.n.u. .c.r.m.-.m.e.g.a.-.m.e.n.u. .b.l.o.c.k.-.c.r.m.-.s.o.l.u.t.i.o.n, div.d.r.o.p.d.o.w.n.-.m.e.n.u. .m.e.g.a.-.m.e.n.u. .i.n.d.u.s.t.r.y.-.c.r.m.-.m.e.g.a.-.m.e.n.u. .b.l.o.c.k.-.c.r.m.-.b.y.-.i.n.d.u.s.t.r.y |
| sticky | 10,12 | div.s.t.a.t.s.-.i.m.a.g.e, div.b.o.t.t.o.m.-.w.a.v.e, span.t.e.x.t.-.p.r.i.m.a.r.y. .b.t.n.-.s.c.r.o.l.l.-.t.o.p. .s.h.a.d.o.w |
| base | -1,5 | img.h.e.r.o.-.b.a.c.k.g.r.o.u.n.d.-.i.m.a.g.e, div.h.e.r.o.-.b.a.c.k.g.r.o.u.n.d, div.s.o.l.u.t.i.o.n.s.-.h.e.r.o.-.s.e.c.t.i.o.n |

**Issues:**
- Very high z-index values: 100000, 200000

## Font Files

| Family | Source | Weights | Styles |
|--------|--------|---------|--------|
| Font Awesome 6 Brands | self-hosted | 400 | normal |
| Font Awesome 6 Duotone | self-hosted | 900 | normal |
| Font Awesome 6 Pro | self-hosted | 100, 300, 400, 900 | normal |
| Font Awesome 5 Brands | self-hosted | 400 | normal |
| Font Awesome 5 Pro | self-hosted | 300, 400, 900 | normal |
| Font Awesome 5 Duotone | self-hosted | 900 | normal |
| FontAwesome | self-hosted | 400, normal | normal |
| Roboto | google-fonts | 300, 400, 500, 700 | normal |

**Google Fonts URL:** `https://fonts.googleapis.com/`

## Image Style Patterns

| Pattern | Count | Key Styles |
|---------|-------|------------|
| thumbnail | 157 | objectFit: fill, borderRadius: 0px, shape: square |
| general | 29 | objectFit: fill, borderRadius: 0px, shape: square |
| hero | 1 | objectFit: cover, borderRadius: 0px, shape: square |

**Aspect ratios:** 21:9 (132x), 3:2 (20x), 16:9 (10x), 1:1 (6x), 4.03:1 (6x), 4:3 (2x), 4.43:1 (1x), 2.13:1 (1x)

## Motion Language

**Feel:** smooth · **Scroll-linked:** yes

### Duration Tokens

| name | value | ms |
|---|---|---|
| `instant` | `80ms` | 80 |
| `xs` | `100ms` | 100 |
| `sm` | `180ms` | 180 |
| `md` | `300ms` | 300 |
| `lg` | `500ms` | 500 |
| `xxl` | `5s` | 5000 |

### Easing Families

- **ease-in-out** (5 uses) — `ease`
- **linear** (4 uses) — `linear`

### Keyframes In Use

| name | kind | properties | uses |
|---|---|---|---|
| `spinner-border` | rotate | transform | 2 |
| `slideDown` | slide-y | opacity, transform | 4 |
| `slideDown` | slide-y | opacity, transform | 4 |
| `pulse` | scale | box-shadow, transform | 1 |
| `pulse` | scale | transform | 1 |
| `fa-spin` | rotate | transform | 1 |
| `fa-spin` | rotate | transform | 1 |
| `lpPopIn` | slide-y | opacity, transform | 2 |
| `lpSpin` | rotate | transform | 2 |

## Component Anatomy

### card — 81 instances


### button — 33 instances

**Slots:** label
**Variants:** link
**Sizes:** small

| variant | count | sample label |
|---|---|---|
| default | 25 | Dùng thử miễn phí |
| link | 8 | Tổng quan |

### input — 2 instances


## Brand Voice

**Tone:** formal · **Pronoun:** third-person · **Headings:** Title Case (verbose)

### Top CTA Verbs

- **xem** (5)
- **tr** (3)
- **t** (2)
- **crm** (2)
- **d** (2)
- **ch** (1)
- **h** (1)
- **gi** (1)

### Button Copy Patterns

- "xem thêm" (4×)
- "dùng thử miễn phí" (2×)
- "tổng quan" (1×)
- "crm tiêu chuẩn" (1×)
- "crm ngành" (1×)
- "trợ lý ai" (1×)
- "chính quyền số" (1×)
- "hạ tầng" (1×)
- "giải pháp khác" (1×)
- "tích hợp" (1×)

### Sample Headings

> CloudGO
Giải pháp CRM & AI chuyên sâu theo ngành
> CHÚNG TÔI HỖ TRỢ DOANH NGHIỆP TRONG CÁC VẤN ĐỀ SAU:
> Tiếp thị đa kênh
> Bán hàng đa kênh
> Chăm sóc khách hàng
> CloudGO
Giải pháp CRM & AI chuyên sâu theo ngành
> CHÚNG TÔI HỖ TRỢ DOANH NGHIỆP TRONG CÁC VẤN ĐỀ SAU:
> Tiếp thị đa kênh
> Bán hàng đa kênh
> Chăm sóc khách hàng

## Page Intent

**Type:** `landing` (confidence 0.75)
**Description:** Với hơn 15 năm kinh nghiệm, CloudGO cung cấp giải pháp CRM & AI chuyên sâu theo từng ngành nghề. Giúp doanh nghiệp tối ưu quy trình kinh doanh và đột phá tăng trưởng. Xem ngay!

## Section Roles

Reading order (top→bottom): nav → nav → testimonial → hero → testimonial → content → testimonials → content → hero → content → testimonial → testimonial → testimonial → hero → content → hero → sidebar → footer

| # | Role | Heading | Confidence |
|---|------|---------|------------|
| 0 | nav | — | 0.4 |
| 1 | nav | — | 0.9 |
| 2 | testimonial | CloudGO
Giải pháp CRM & AI chuyên sâu theo ngành | 0.8 |
| 3 | hero | CloudGO
Giải pháp CRM & AI chuyên sâu theo ngành | 0.85 |
| 4 | testimonial | Bài viết nổi bật | 0.8 |
| 5 | content | SIÊU ỨNG DỤNG CRM “5 IN 1” | 0.3 |
| 6 | testimonials | Lựa chọn giải pháp chuyển đổi số phù hợp với nhu cầu doanh nghiệp của bạn | 0.4 |
| 7 | content | Tại sao hàng nghìn doanh nghiệp đã chọn CloudGO? | 0.3 |
| 8 | hero | Hành trình bứt phá cùng doanh nghiệp trên con đường chuyển đổi số | 0.4 |
| 9 | content | THÀNH VIÊN CỦA HIỆP HỘI VÀ CỘNG ĐỒNG | 0.3 |
| 10 | testimonial | Cảm ơn khách hàng đã tin tưởng và lựa chọn CloudGO! | 0.8 |
| 11 | testimonial | CloudGO đi muôn nơi... | 0.8 |
| 12 | testimonial | CloudGO Blog | 0.8 |
| 13 | hero | Đối tác tiêu biểu | 0.4 |
| 14 | content | Báo chí, truyền hình nói về CloudGO | 0.3 |
| 15 | hero | CloudGO - Giải pháp CRM & AI chuyên sâu theo ngành | 0.4 |
| 16 | sidebar | — | 0.4 |
| 17 | footer | — | 0.95 |

## Material Language

**Label:** `flat` (confidence 0)

| Metric | Value |
|--------|-------|
| Avg saturation | 0.313 |
| Shadow profile | soft |
| Avg shadow blur | 0px |
| Max radius | 100px |
| backdrop-filter in use | no |
| Gradients | 11 |

## Imagery Style

**Label:** `photography` (confidence 0.089)
**Counts:** total 187, svg 6, icon 22, screenshot-like 0, photo-like 17
**Dominant aspect:** ultra-wide
**Radius profile on images:** square

## Component Library

**Detected:** `bootstrap` (confidence 0.7)

Evidence:
- bootstrap utility hits: 4

## Quick Start

To recreate this design in a new project:

1. **Install fonts:** Add `Roboto` from Google Fonts or your font provider
2. **Import CSS variables:** Copy `variables.css` into your project
3. **Tailwind users:** Use the generated `tailwind.config.js` to extend your theme
4. **Design tokens:** Import `design-tokens.json` for tooling integration
