# Design Language: Student Management Software for Schools & Universities – Classe365

> Extracted from `https://www.classe365.com/` on October 31, 2026
> 999 elements analyzed across 5 pages

This document describes the complete design language of the website. It is structured for AI/LLM consumption — use it to faithfully recreate the visual design in any framework.

## Color Palette

### Primary Colors

| Role | Hex | RGB | HSL | Usage Count |
|------|-----|-----|-----|-------------|
| Primary | `#10306f` | rgb(16, 48, 111) | hsl(220, 75%, 25%) | 233 |
| Secondary | `#6772e5` | rgb(103, 114, 229) | hsl(235, 71%, 65%) | 70 |
| Accent | `#d27164` | rgb(210, 113, 100) | hsl(7, 55%, 61%) | 1 |

### Tonal Ramps

Generated in OKLCH from each role colour, so the steps are perceptually even. The site's own colour keeps its position in the ladder.

| Role | 50 | 100 | 200 | 300 | 400 | 500 | 600 | 700 | 800 | 900 | 950 |
|------|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|-----|
| primary | `#dcf7ff` | `#ceebff` | `#badaff` | `#9dc1ff` | `#799fe5` | `#5b81c7` | `#486bac` | `#36558f` | `#2a4578` | **`#10306f`** | `#0e1d3c` |
| secondary | `#e2f0ff` | `#d5e4ff` | `#c2d2ff` | `#a7b7ff` | `#8593ff` | **`#6772e5`** | `#555ec9` | `#4349a9` | `#353a8f` | `#2a2d77` | `#151649` |
| accent | `#ffe3d9` | `#ffd6cb` | `#ffc2b6` | `#fda699` | **`#d27164`** | `#c16256` | `#a64e43` | `#893b32` | `#732d26` | `#5f221c` | `#380f0b` |
| neutral | `#f5f5f5` | `#eaeaea` | `#d9d9d9` | `#c0c0c0` | `#9f9f9f` | `#828282` | `#6c6c6c` | **`#555555`** | `#464646` | `#393939` | `#1f1f1f` |

### Semantic Pairs

| Role | Background | Foreground | Contrast | Level |
|------|------------|------------|----------|-------|
| `surface.page` | `#ffffff` | `#000000` | 21:1 | AAA |
| `surface.raised` | `#000000` | `#ffffff` | 21:1 | AAA |
| `action.primary` | `#10306f` | `#ffffff` | 12.54:1 | AAA |
| `action.secondary` | `#6772e5` | `#000000` | 5.12:1 | AA |
| `action.accent` | `#d27164` | `#000000` | 6.29:1 | AA |

### Dominance

Share of painted background area — what the page looks like, as opposed to what it declares most often.

- `#f2f9ff` — 41%
- `#e2efff` — 21%
- `#ffffff` — 20%
- `#6772e5` — 13%
- `#000000` — 3%
- `#555555` — 1%

### Neutral Colors

| Hex | HSL | Usage Count |
|-----|-----|-------------|
| `#000000` | hsl(0, 0%, 0%) | 780 |
| `#ffffff` | hsl(0, 0%, 100%) | 295 |
| `#555555` | hsl(0, 0%, 33%) | 271 |
| `#101010` | hsl(0, 0%, 6%) | 108 |
| `#7a95b0` | hsl(210, 25%, 58%) | 44 |
| `#f2f9ff` | hsl(208, 100%, 97%) | 10 |
| `#8b97a7` | hsl(214, 14%, 60%) | 8 |
| `#808080` | hsl(0, 0%, 50%) | 3 |
| `#6f767e` | hsl(212, 6%, 46%) | 2 |
| `#ffe5f4` | hsl(325, 100%, 95%) | 2 |
| `#fff0e5` | hsl(25, 100%, 95%) | 1 |
| `#5b5e81` | hsl(235, 17%, 43%) | 1 |

### Background Colors

Used on large-area elements: `#ffffff`, `#000000`, `#6772e5`, `#e2efff`, `#555555`, `#13b9ff`

### Text Colors

Text color palette: `#6f767e`, `#000000`, `#101010`, `#3b3f42`, `#0000ee`, `#ffffff`, `#0b6be6`, `#6772e5`, `#1e2123`, `#10306f`

### Gradients

```css
background-image: linear-gradient(90deg, rgb(103, 114, 229) 0.07%, rgb(26, 165, 244) 99.93%);
```

```css
background-image: linear-gradient(90deg, rgb(103, 114, 229), rgb(26, 165, 244));
```

```css
background-image: linear-gradient(90deg, rgba(103, 114, 229, 0.21), rgba(26, 165, 244, 0.21));
```

### Full Color Inventory

| Hex | Contexts | Count |
|-----|----------|-------|
| `#000000` | text, border, background | 864 |
| `#555555` | text, border, background | 820 |
| `#ffffff` | background, text, border | 304 |
| `#101010` | text, border | 108 |
| `#6772e5` | background, border, text | 95 |
| `#e6e8eb` | background, border | 20 |
| `#43617f` | text, border | 10 |
| `#242424` | text, border | 1543 |
| `#3b3f42` | text, border | 797 |
| `#10306f` | text, border, background | 233 |
| `#808080` | text, border, background | 191 |
| `#6b7075` | text, border | 42 |
| `#0000ee` | text, border | 12 |
| `#0b6be6` | text, border | 2 |
| `#98b5cd` | border | 13 |
| `#ded4ff` | background | 1 |
| `#cc3366` | text, border | 183 |
| `#ffbb4d` | text, border | 58 |
| `#7a95b0` | text, border | 44 |
| `#2e3192` | text, border | 46 |
| `#6ec1e4` | text, border | 10 |
| `#a7a9ac` | text, border | 6 |
| `#2d6ccb` | text, border | 6 |
| `#d5d8dc` | text, border | 6 |
| `#292c45` | background | 4 |
| `#a0932b` | text, border | 2 |
| `#666ebb` | text, border | 2 |
| `#816cc1` | text, border | 2 |
| `#bc8257` | text, border | 2 |
| `#4f87b7` | text, border | 2 |

## Typography

### Font Families

- **Inter** — used for all (591 elements)
- **Poppins** — used for all (259 elements)
- **Inter-Regular_43** — used for body (85 elements)
- **Inter-Medium_3y** — used for body (19 elements)
- **Inter-SemiBold_48** — used for body (12 elements)
- **Inter-Bold_3x** — used for all (8 elements)
- **Inter-ExtraBold_3z** — used for body (4 elements)
- **Inter-Bold_5m** — used for body (2 elements)

### Type System

- **Scale ratio** — 1.067 (minor second), anchored at 13px · 100% of sizes sit on the ladder
- **Measure** — ~38 characters per line (narrow)
- **Fluid type** — 1 clamp/viewport font sizes

### Named Scale

| Role | Size | Weight | Line Height | Tracking |
|------|------|--------|-------------|----------|
| `h6` | 56px / 3.5rem | 700 | normal | normal |
| `h1` | 48px / 3rem | 700 | 59px | normal |
| `title-1` | 44px / 2.75rem | 700 | normal | -1px |
| `h2` | 42px / 2.625rem | 700 | 51px | normal |
| `title-2` | 40px / 2.5rem | 600 | 47px | normal |
| `title-3` | 36px / 2.25rem | 400 | 36px | normal |
| `title-4` | 30px / 1.875rem | 400 | normal | normal |
| `title-5` | 28px / 1.75rem | 700 | 33.6px | normal |
| `title-6` | 26px / 1.625rem | 500 | normal | normal |
| `title-7` | 24px / 1.5rem | 400 | normal | normal |
| `title-8` | 22px / 1.375rem | 600 | 36px | normal |
| `title-9` | 20px / 1.25rem | 400 | 31px | normal |
| `h3` | 18px / 1.125rem | 600 | normal | normal |
| `title-10` | 17px / 1.0625rem | 500 | 21px | normal |
| `title-11` | 16.1429px / 1.0089rem | 600 | 15px | normal |

### Fluid Declarations

```css
.iAEpwo :root { font-size: calc(14.8571px + 0.357143vw); }
```

### Type Scale

| Size (px) | Size (rem) | Weight | Line Height | Letter Spacing | Used On |
|-----------|------------|--------|-------------|----------------|---------|
| 56px | 3.5rem | 700 | normal | normal | h6 |
| 50px | 3.125rem | 500 | 60px | 0.5px | h1 |
| 48px | 3rem | 700 | 59px | normal | h1 |
| 45px | 2.8125rem | 700 | 67.5px | normal | h2 |
| 44px | 2.75rem | 700 | normal | -1px | div |
| 42px | 2.625rem | 700 | 51px | normal | h2 |
| 40px | 2.5rem | 600 | 47px | normal | p |
| 36px | 2.25rem | 400 | 36px | normal | div, span, p |
| 34px | 2.125rem | 400 | 30px | normal | span, i |
| 32px | 2rem | 700 | normal | normal | h2 |
| 30px | 1.875rem | 400 | normal | normal | div, svg, path, p |
| 29px | 1.8125rem | 700 | 43.5px | normal | h3 |
| 28px | 1.75rem | 700 | 33.6px | normal | div, span |
| 26px | 1.625rem | 700 | normal | normal | h2 |
| 24px | 1.5rem | 400 | normal | normal | div, svg, path, h2 |

### Heading Scale

```css
h6 { font-size: 56px; font-weight: 700; line-height: normal; }
h1 { font-size: 48px; font-weight: 700; line-height: 59px; }
h2 { font-size: 42px; font-weight: 700; line-height: 51px; }
h3 { font-size: 18px; font-weight: 600; line-height: normal; }
h5 { font-size: 16px; font-weight: 400; line-height: normal; }
```

### Body Text

```css
body { font-size: 14px; font-weight: 500; line-height: normal; }
```

### Font Weights in Use

`400` (658x), `500` (157x), `600` (151x), `700` (32x), `300` (1x)

## Spacing

| Token | Value | Rem |
|-------|-------|-----|
| spacing-1 | 1px | 0.0625rem |
| spacing-2 | 2px | 0.125rem |
| spacing-3 | 3px | 0.1875rem |
| spacing-4 | 4px | 0.25rem |
| spacing-5 | 5px | 0.3125rem |
| spacing-8 | 8px | 0.5rem |
| spacing-14 | 14px | 0.875rem |
| spacing-16 | 16px | 1rem |
| spacing-18 | 18px | 1.125rem |
| spacing-23 | 23px | 1.4375rem |
| spacing-24 | 24px | 1.5rem |
| spacing-27 | 27px | 1.6875rem |
| spacing-28 | 28px | 1.75rem |
| spacing-30 | 30px | 1.875rem |
| spacing-35 | 35px | 2.1875rem |
| spacing-38 | 38px | 2.375rem |
| spacing-39 | 39px | 2.4375rem |
| spacing-40 | 40px | 2.5rem |
| spacing-43 | 43px | 2.6875rem |
| spacing-44 | 44px | 2.75rem |

## Border Radii

| Label | Value | Count |
|-------|-------|-------|
| sm | 3px | 4 |
| sm | 4px | 9 |
| md | 6px | 23 |
| md | 8px | 22 |
| lg | 12px | 34 |
| xl | 19px | 6 |
| xl | 24px | 3 |
| full | 30px | 1 |
| full | 31px | 11 |
| full | 36px | 7 |
| full | 45px | 1 |
| full | 50px | 19 |
| full | 52px | 1 |
| full | 55px | 1 |
| full | 60px | 2 |
| full | 69px | 1 |
| full | 89px | 1 |
| full | 100px | 20 |
| full | 276px | 3 |
| full | 999px | 12 |
| full | 9999px | 12 |

## Elevation

4 levels from 6 raw shadows · neutral tint

**xs** — blur 8px, y 2px, used 1× · tinted (hue 233°)
```css
box-shadow: rgba(108, 122, 224, 0.133) 0px 2px 8px 0px;
```

**sm** — blur 16px, y 2px, used 1×
```css
box-shadow: rgba(26, 26, 26, 0.133) 0px 2px 16px 0px;
```

**md** — blur 20px, y 4px, used 16×
```css
box-shadow: rgba(84, 84, 84, 0.25) 0px 4px 20px 0px;
```

**lg** — blur 26px, y 65px, used 1× · 4 layers
```css
box-shadow: rgba(194, 194, 194, 0.01) 0px 65px 26px 0px, rgba(194, 194, 194, 0.05) 0px 36px 22px 0px, rgba(194, 194, 194, 0.09) 0px 16px 16px 0px, rgba(194, 194, 194, 0.1) 0px 4px 9px 0px;
```

## Inset Shadows

```css
box-shadow: rgba(255, 255, 255, 0.05) 0px 0px 6px 0px inset, rgba(0, 0, 0, 0.03) 0px 0px 6px 0px inset;
```

## CSS Custom Properties

### Colors

```css
--swiper-theme-color: #007aff;
```

### Spacing

```css
--swiper-navigation-size: 44px;
```

## Breakpoints

| Name | Value | Type |
|------|-------|------|
| sm | 425px | max-width |
| sm | 480px | max-width |
| sm | 481px | min-width |
| sm | 520px | max-width |
| sm | 586px | max-width |
| md | 767px | max-width |
| md | 768px | max-width |
| md | 769px | min-width |
| lg | 992px | max-width |
| lg | 1024px | max-width |
| 1140px | 1140px | max-width |
| 1200px | 1200px | max-width |
| xl | 1280px | max-width |
| 1400px | 1400px | max-width |
| 2xl | 1500px | max-width |
| 2xl | 1600px | max-width |

## Transitions & Animations

**Easing functions:** `ease`, `cubic-bezier(0.25, 0.46, 0.45, 0.94)`

**Durations:** `0.3s`, `0.2s`, `0.6s`, `1s`

### Common Transitions

```css
transition: all;
transition: transform 0.3s ease-in-out;
transition: width 0.3s, padding 0.3s;
transition: 0.3s ease-in-out;
transition: 0.3s;
transition: 0.2s ease-in-out;
transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
transition: transform 1s, opacity 1s;
transition: transform 0.3s;
```

### Keyframe Animations

**bubbleAnim**
```css
@keyframes bubbleAnim {
  50% { transform: translateY(-10px) rotate(26.368deg); }
}
```

**scaleAnim**
```css
@keyframes scaleAnim {
  50% { transform: scale(1.2); }
}
```

**rotateAnim**
```css
@keyframes rotateAnim {
  0% { transform: rotate(0deg) translateY(0px); }
  50% { transform: rotate(180deg) translateY(-10px); }
  100% { transform: rotate(1turn) translateY(0px); }
}
```

**button-spin**
```css
@keyframes button-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(1turn); }
}
```

**slideLeft**
```css
@keyframes slideLeft {
  0% { transform: translateX(-100%); opacity: 0; }
  100% { transform: translateX(0px); opacity: 1; }
}
```

**slideRight**
```css
@keyframes slideRight {
  0% { transform: translateX(100%); opacity: 0; }
  100% { transform: translateX(0px); opacity: 1; }
}
```

**swiper-preloader-spin**
```css
@keyframes swiper-preloader-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(1turn); }
}
```

**nprogress-spinner**
```css
@keyframes nprogress-spinner {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(1turn); }
}
```

**line1**
```css
@keyframes line1 {
  0%, 8% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  18% { stroke-dashoffset: 16; transform: translate(0px, 8px); }
  28% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  38% { stroke-dashoffset: 0; transform: translate(0px, 0px); }
  48% { opacity: 1; stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  53% { opacity: 0; stroke-dashoffset: 31.99; transform: translate(8px, 16px); }
  56% { animation-timing-function: steps(1, start); opacity: 0; stroke-dashoffset: 32; transform: translate(0px, 16px); }
  60% { animation-timing-function: ease-out; opacity: 1; stroke-dashoffset: 32; transform: translate(0px, 16px); }
  70% { animation-timing-function: ease-in-out; stroke-dashoffset: 0; transform: translate(0px, 0px); }
  80% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  90% { stroke-dashoffset: 16; transform: translate(0px, 8px); }
  100% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
}
```

**line2**
```css
@keyframes line2 {
  0%, 6% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  16% { stroke-dashoffset: 16; transform: translate(0px, 8px); }
  26% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  36% { stroke-dashoffset: 0; transform: translate(0px, 0px); }
  46% { opacity: 1; stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  51% { opacity: 0; stroke-dashoffset: 31.99; transform: translate(8px, 16px); }
  54% { animation-timing-function: steps(1, start); opacity: 0; stroke-dashoffset: 32; transform: translate(0px, 16px); }
  58% { animation-timing-function: ease-out; opacity: 1; stroke-dashoffset: 32; transform: translate(0px, 16px); }
  68% { animation-timing-function: ease-in-out; stroke-dashoffset: 0; transform: translate(0px, 0px); }
  78% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
  88% { stroke-dashoffset: 16; transform: translate(0px, 8px); }
  98%, 100% { stroke-dashoffset: 31.99; transform: translate(0px, 16px); }
}
```

## Component Patterns

Detected UI component patterns and their most common styles:

### Buttons (26 instances)

```css
.buttons {
  background-color: rgb(255, 255, 255);
  color: rgb(255, 255, 255);
  font-size: 16.1429px;
  font-weight: 600;
  padding-top: 10px;
  padding-right: 23px;
  border-radius: 31.1538px;
}
```

### Cards (93 instances)

```css
.cards {
  background-color: rgb(255, 255, 255);
  border-radius: 0px;
  box-shadow: rgba(84, 84, 84, 0.25) 0px 4px 20px 0px;
  padding-top: 0px;
  padding-right: 0px;
}
```

### Links (82 instances)

```css
.links {
  color: rgb(16, 16, 16);
  font-size: 14px;
  font-weight: 400;
}
```

### Navigation (69 instances)

```css
.navigation {
  background-color: rgb(242, 242, 242);
  color: rgb(0, 0, 0);
  padding-top: 0px;
  padding-bottom: 0px;
  padding-left: 0px;
  padding-right: 0px;
  position: static;
  box-shadow: rgba(255, 255, 255, 0.05) 0px 0px 6px 0px inset, rgba(0, 0, 0, 0.03) 0px 0px 6px 0px inset;
}
```

### Footer (3 instances)

```css
.footer {
  color: rgb(0, 0, 0);
  padding-top: 0px;
  padding-bottom: 0px;
  font-size: 16px;
}
```

### Modals (2 instances)

```css
.modals {
  background-color: rgba(0, 0, 0, 0.314);
  border-radius: 0px;
  padding-top: 0px;
  padding-right: 0px;
}
```

### Dropdowns (13 instances)

```css
.dropdowns {
  border-radius: 0px;
  border-color: rgb(0, 0, 0);
  padding-top: 0px;
}
```

### Badges (17 instances)

```css
.badges {
  color: rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 600;
  padding-top: 0px;
  padding-right: 0px;
  border-radius: 0px;
}
```

### Avatars (5 instances)

```css
.avatars {
  border-radius: 8px;
  background-color: rgb(255, 255, 255);
}
```

### Tabs (18 instances)

```css
.tabs {
  background-color: rgb(255, 255, 255);
  color: rgb(255, 255, 255);
  font-size: 13px;
  font-weight: 500;
  padding-top: 0px;
  padding-right: 0px;
  border-color: rgb(255, 255, 255);
  border-radius: 0px;
}
```

## Component Clusters

Reusable component instances grouped by DOM structure and style similarity:

### Button — 3 instances, 2 variants

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 8px 18px 8px 18px;
  border-radius: 100px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 600;
```

**Variant 2** (2 instances)

```css
  background: rgb(228, 228, 228);
  color: rgb(16, 16, 16);
  padding: 8px 18px 8px 18px;
  border-radius: 52px;
  border: 0px none rgb(16, 16, 16);
  font-size: 16px;
  font-weight: 600;
```

### Button — 3 instances, 2 variants

**Variant 1** (1 instance)

```css
  background: rgb(11, 18, 32);
  color: rgb(255, 255, 255);
  padding: 12px 16px 12px 16px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.06);
  font-size: 13.3333px;
  font-weight: 400;
```

**Variant 2** (2 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(11, 107, 230);
  padding: 12px 16px 12px 16px;
  border-radius: 8px;
  border: 1px solid rgb(11, 107, 230);
  font-size: 13.3333px;
  font-weight: 400;
```

### Button — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 5 instances, 2 variants

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 10px 23px 10px 23px;
  border-radius: 100px;
  border: 0px none rgb(255, 255, 255);
  font-size: 18px;
  font-weight: 600;
```

**Variant 2** (3 instances)

```css
  background: rgb(255, 255, 255);
  color: rgb(103, 114, 229);
  padding: 10px 23px 10px 23px;
  border-radius: 100px;
  border: 0px none rgb(103, 114, 229);
  font-size: 18px;
  font-weight: 600;
```

### Button — 6 instances, 1 variant

**Variant 1** (6 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(255, 255, 255);
  font-size: 18px;
  font-weight: 600;
```

### Card — 11 instances, 1 variant

**Variant 1** (11 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 5 instances, 1 variant

**Variant 1** (5 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 10px 23px 10px 23px;
  border-radius: 31.1538px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16.1429px;
  font-weight: 600;
```

### Card — 5 instances, 1 variant

**Variant 1** (5 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 3 instances, 2 variants

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(16, 48, 111);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(16, 48, 111);
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

### Card — 7 instances, 1 variant

**Variant 1** (7 instances)

```css
  background: rgb(239, 246, 255);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 36px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 4 instances, 1 variant

**Variant 1** (4 instances)

```css
  background: rgb(255, 218, 212);
  color: rgb(0, 0, 0);
  padding: 12px 44px 12px 44px;
  border-radius: 100px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 8 instances, 2 variants

**Variant 1** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(16, 48, 111);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(16, 48, 111);
  font-size: 40px;
  font-weight: 600;
```

**Variant 2** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(139, 151, 167);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(139, 151, 167);
  font-size: 22px;
  font-weight: 400;
```

### Card — 4 instances, 1 variant

**Variant 1** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 4 instances, 1 variant

**Variant 1** (4 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgba(0, 0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgba(0, 0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 7 instances, 1 variant

**Variant 1** (7 instances)

```css
  background: rgb(103, 114, 229);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 36px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Card — 6 instances, 1 variant

**Variant 1** (6 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 2 instances, 1 variant

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 1px 6px 1px 6px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 13.3333px;
  font-weight: 400;
```

### Card — 6 instances, 1 variant

**Variant 1** (6 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Link — 2 instances, 1 variant

**Variant 1** (2 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 238);
  padding: 0px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 238);
  font-size: 16px;
  font-weight: 400;
```

### Card — 5 instances, 1 variant

**Variant 1** (5 instances)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(0, 0, 0);
  padding: 0px 14px 0px 14px;
  border-radius: 0px;
  border: 0px none rgb(0, 0, 0);
  font-size: 16px;
  font-weight: 400;
```

### Button — 1 instance, 1 variant

**Variant 1** (1 instance)

```css
  background: rgba(0, 0, 0, 0);
  color: rgb(255, 255, 255);
  padding: 100px 0px 0px 0px;
  border-radius: 0px;
  border: 0px none rgb(255, 255, 255);
  font-size: 16px;
  font-weight: 400;
```

## Layout System

**2 grid containers** and **187 flex containers** detected.

### The System

- **Content column** — 1120px wide
  - Width ladder: 363px · 704px · 1000px · 1120px · 1280px
- **Column system** — 5 columns
- **Section rhythm** — 30px vertical padding (ladder: 30px · 53px · 70px · 107px)
- **Gap scale** — 3px · 8px · 12px · 14px · 16px · 20px · 23px · 48px
- **Fluid layout** — 1 clamp/viewport declarations

```css
.iAEpwo div { width: 100vw; }
```

### Container Widths

| Max Width | Padding |
|-----------|---------|
| 1376px | 0px |
| 1000px | 0px |
| 1320px | 80px |

### Grid Column Patterns

| Columns | Usage Count |
|---------|-------------|
| 3-column | 1x |
| 5-column | 1x |

### Grid Templates

```css
grid-template-columns: 172px 172px 172px 172px 236px;
gap: 49px;
grid-template-columns: 304px 304px 304px;
gap: 20px;
```

### Flex Patterns

| Direction/Wrap | Count |
|----------------|-------|
| row/nowrap | 147x |
| column/nowrap | 34x |
| column-reverse/nowrap | 1x |
| row/wrap | 5x |

**Gap values:** `3px`, `4px`, `8px`, `12px`, `14px`, `16px`, `20px`, `21px`, `23px`, `30px`, `48px`, `49px`, `100px`

## Accessibility (WCAG 2.1)

**Overall Score: 27%** — 3 passing, 8 failing color pairs

### Failing Color Pairs

| Foreground | Background | Ratio | Level | Used On |
|------------|------------|-------|-------|---------|
| `#a0932b` | `#fffbd9` | 2.99:1 | FAIL | p (1x) |
| `#ffe2de` | `#d27164` | 2.73:1 | FAIL | p (1x) |
| `#ffe5f4` | `#a25c84` | 4.04:1 | FAIL | p (1x) |
| `#666ebb` | `#e2e5ff` | 3.74:1 | FAIL | p (1x) |
| `#816cc1` | `#eae3ff` | 3.49:1 | FAIL | p (1x) |
| `#bc8257` | `#fff0e5` | 2.91:1 | FAIL | p (1x) |
| `#4f87b7` | `#dbeeff` | 3.23:1 | FAIL | p (1x) |
| `#4b99ba` | `#d8f3ff` | 2.77:1 | FAIL | p (1x) |

### Passing Color Pairs

| Foreground | Background | Ratio | Level |
|------------|------------|-------|-------|
| `#ffffff` | `#0b1220` | 18.72:1 | AAA |
| `#0b6be6` | `#ffffff` | 4.93:1 | AA |
| `#6c7ae0` | `#ffffff` | 3.82:1 | AA |

## Design System Score

**Overall: 62/100 (Grade: D)**

| Category | Score |
|----------|-------|
| Color Discipline | 80/100 |
| Typography Consistency | 82/100 |
| Spacing System | 55/100 |
| Shadow Consistency | 90/100 |
| Border Radius Consistency | 65/100 |
| Accessibility | 27/100 |
| CSS Tokenization | 50/100 |

**Strengths:** Clean elevation system

**Issues:**
- No consistent spacing base unit detected — values appear arbitrary
- 8 WCAG contrast failures
- 217 !important rules — prefer specificity over overrides
- 88% of CSS is unused — consider purging
- 3736 duplicate CSS declarations

## Gradients

**3 unique gradients** detected.

| Type | Direction | Stops | Classification |
|------|-----------|-------|----------------|
| linear | 90deg | 2 | brand |
| linear | 90deg | 2 | brand |
| linear | 90deg | 2 | brand |

```css
background: linear-gradient(90deg, rgb(103, 114, 229) 0.07%, rgb(26, 165, 244) 99.93%);
background: linear-gradient(90deg, rgb(103, 114, 229), rgb(26, 165, 244));
background: linear-gradient(90deg, rgba(103, 114, 229, 0.21), rgba(26, 165, 244, 0.21));
```

## Z-Index Map

**8 unique z-index values** across 3 layers.

| Layer | Range | Elements |
|-------|-------|----------|
| dropdown | 100,104 | div.M.o.b.i.l.e.N.a.v.b.a.r.C.o.n.t.a.i.n.e.r, div.i.n.f.o.-.c.a.r.d.-.c.o.n.t.a.i.n.e.r. .s.t.i.c.k.y.-.c.a.r.d.-.1, div.i.n.f.o.-.c.a.r.d.-.c.o.n.t.a.i.n.e.r. .s.t.i.c.k.y.-.c.a.r.d.-.2 |
| sticky | 10,90 | div.n.e.w.H.e.a.d.e.r.W.r.a.p.p.e.r, div.N.a.v.I.t.e.m.W.r.a.p.p.e.r, div.N.a.v.I.t.e.m.W.r.a.p.p.e.r |
| base | 1,1 | div.h.e.r.o.-.b.u.t.t.o.n.s, div.t.e.s.t.i.m.o.n.i.a.l.-.m.a.i.n.-.c.o.n.t.a.i.n.e.r, div.l.e.f.t.-.c.o.n.t.e.n.t.-.b.l.o.c.k |

## SVG Icons

**25 unique SVG icons** detected. Dominant style: **duo-tone**.

| Size Class | Count |
|------------|-------|
| xs | 3 |
| sm | 8 |
| md | 4 |
| lg | 8 |
| xl | 2 |

**Icon colors:** `white`, `currentColor`, `#010101`, `#6772E5`, `#6F79E6`, `#10306F`, `#F6CB1E`

## Font Files

| Family | Source | Weights | Styles |
|--------|--------|---------|--------|
| Inter | cdn | 300, 400, 500, 600, 700, 800 | normal |
| Roboto Slab | cdn | 200, 300, 400, 500, 600, 700, 800 | normal |
| Inter-Bold_3x | self-hosted | 400, normal | normal |
| Inter-ExtraBold_3z | self-hosted | 400, normal | normal |
| Inter-Medium_3y | self-hosted | 400, normal | normal |
| Inter-Regular_43 | self-hosted | 400, normal | normal |
| Inter-SemiBold_48 | self-hosted | 400, normal | normal |
| swiper-icons | self-hosted | 400 | normal |

## Image Style Patterns

| Pattern | Count | Key Styles |
|---------|-------|------------|
| thumbnail | 41 | objectFit: fill, borderRadius: 0px, shape: square |
| general | 13 | objectFit: fill, borderRadius: 26px, shape: pill |
| gallery | 4 | objectFit: fill, borderRadius: 0px, shape: square |

**Aspect ratios:** 1:1 (26x), 16:9 (8x), 4:3 (7x), 3:2 (4x), 5.44:1 (1x), 3.33:1 (1x), 5.15:1 (1x), 2.52:1 (1x)

## Motion Language

**Feel:** smooth · **Scroll-linked:** yes

### Duration Tokens

| name | value | ms |
|---|---|---|
| `sm` | `200ms` | 200 |
| `md` | `300ms` | 300 |
| `lg` | `600ms` | 600 |
| `xl` | `1s` | 1000 |

### Easing Families

- **ease-in-out** (11 uses) — `ease`
- **ease-out** (1 uses) — `cubic-bezier(0.25, 0.46, 0.45, 0.94)`

## Component Anatomy

### card — 68 instances

**Slots:** media
**Variants:** primary

| variant | count | sample label |
|---|---|---|
| default | 64 | Marketing
Boost Enrolments with Ease
Wan |
| primary | 4 | Boost enrolment, streamline operations,  |

### button — 30 instances

**Slots:** label
**Variants:** primary · secondary
**Sizes:** lg

| variant | count | sample label |
|---|---|---|
| default | 23 | Try for Free |
| primary | 6 | Watch Demo |
| secondary | 1 | Contact sales |

### link — 2 instances


## Brand Voice

**Tone:** friendly · **Pronoun:** third-person · **Headings:** unknown (tight)

### Top CTA Verbs

- **watch** (10)
- **try** (7)
- **learn** (7)
- **contact** (1)
- **ready** (1)
- **check** (1)
- **join** (1)
- **discover** (1)

### Button Copy Patterns

- "watch demo" (10×)
- "learn more" (7×)
- "try for free" (6×)
- "contact sales" (1×)
- "try for free
watch demo" (1×)
- "ready for takeoff
scale fast, spend smart — just pay for what you use.
check our pricing page" (1×)
- "check our pricing page" (1×)
- "measuring & communicating the value of higher education

from redefining success metrics to the role of edtech, join us for insights shaping..." (1×)
- "discover how classe365 can simplify institution management

removing data silos and unifying platforms with classe365" (1×)
- "start with

$100/month
watch demo" (1×)

## Page Intent

**Type:** `landing` (confidence 0.45)
**Description:** Classe365 offers powerful student management software to streamline administration for schools and colleges. Discover the smarter way to manage students—start today!

## Material Language

**Label:** `material-you` (confidence 0.45)

| Metric | Value |
|--------|-------|
| Avg saturation | 0.344 |
| Shadow profile | soft |
| Avg shadow blur | 0px |
| Max radius | 9999px |
| backdrop-filter in use | no |
| Gradients | 3 |

## Imagery Style

**Label:** `screenshot` (confidence 0.046)
**Counts:** total 58, svg 4, icon 15, screenshot-like 2, photo-like 1
**Dominant aspect:** square-ish
**Radius profile on images:** square

## Quick Start

To recreate this design in a new project:

1. **Install fonts:** Add `Inter` from Google Fonts or your font provider
2. **Import CSS variables:** Copy `variables.css` into your project
3. **Tailwind users:** Use the generated `tailwind.config.js` to extend your theme
4. **Design tokens:** Import `design-tokens.json` for tooling integration
