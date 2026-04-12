# Design System Strategy: The Ethereal Clinical

## 1. Overview & Creative North Star
In the world of medical systems, "standard" often translates to "sterile" and "intimidating." This design system rejects the clinical coldness of traditional software, instead embracing a Creative North Star we call **"The Digital Sanctuary."**

This system is built to feel fresh, pleasant, and weightless. By moving away from rigid grids and heavy borders, we create an editorial-inspired medical ecosystem. We achieve this through **Organic Layering**—utilizing subtle tonal shifts and deep, ambient shadows to create a UI that feels like light passing through soft-touch materials. The goal is to reduce cognitive load for medical professionals while maintaining an atmosphere of calm authority.

---

## 2. Colors: Tonal Depth vs. Structural Lines
Our palette is anchored in high-energy lavenders (`primary`) and stabilized by deep medical blues (`secondary`). This creates a balance between innovation and reliability.

### The "No-Line" Rule
To maintain the "Sanctuary" feel, **1px solid borders are prohibited for sectioning.** Boundaries must be defined solely through background color shifts. For example, a sidebar using `surface-container-low` should sit against a `surface` background without a dividing line. 

### Surface Hierarchy & Nesting
Treat the UI as a physical stack of premium materials.
*   **Base:** `surface` (#f7f9fc)
*   **Secondary Content Areas:** `surface-container-low` (#f1f4f7)
*   **Interactive Cards:** `surface-container-lowest` (#ffffff) to provide "pop" and focus.
*   **High-Impact Overlays:** Use `surface-bright` for floating modals to catch light.

### The "Glass & Gradient" Rule
Standard flat buttons feel archaic. Main CTAs and high-profile containers should utilize **Signature Textures**. 
*   **Primary Action Gradient:** Linear transition from `primary` (#7330e6) to `primary_dim` (#661ada) at a 135-degree angle.
*   **Glassmorphism:** For floating search bars or navigation headers, use `surface` with 80% opacity and a `24px` backdrop-blur. This allows the medical blues and purples to bleed through, keeping the experience integrated.

---

## 3. Typography: Editorial Precision
The system utilizes a dual-typeface strategy to combine high-end editorial flair with clinical readability.

*   **Display & Headlines (Plus Jakarta Sans):** These are our "Voice." They use generous tracking and bold weights to provide a sense of modern confidence.
*   **Body & Labels (Manrope):** Our "Function." Manrope’s geometric but open counters ensure that patient data and medical records remain legible even at the smallest `label-sm` (0.6875rem) sizes.

**Visual Hierarchy Tip:** Use `primary` (#7330e6) for `title-sm` elements when they act as interactive anchors, but keep `body-lg` in `on_surface` (#2d3338) for maximum reading comfort.

---

## 4. Elevation & Depth
Depth in this system is achieved through **Tonal Layering** rather than traditional drop shadows.

*   **The Layering Principle:** Place a `surface-container-lowest` card atop a `surface-container-high` section to create natural lift. The contrast in value creates the edge, not a stroke.
*   **Ambient Shadows:** If an element must "float" (like a global action button), use an extra-diffused shadow: `0px 12px 32px rgba(115, 48, 230, 0.08)`. Notice we tint the shadow with the `primary` color to mimic natural light refraction.
*   **The "Ghost Border" Fallback:** If accessibility requires a border, use `outline_variant` at **20% opacity**. It should be felt, not seen.
*   **Corner Softness:** Follow the `xl` (1.5rem) scale for large containers and `md` (0.75rem) for interactive components to maintain the "pleasant" aesthetic.

---

## 5. Components

### Buttons
*   **Primary:** High-softness (`full` or `xl` roundedness). Uses the Signature Gradient.
*   **Secondary:** `secondary_container` background with `on_secondary_container` text. No border.
*   **Tertiary:** Transparent background, `primary` text, with a `surface-variant` hover state.

### Input Fields
*   Forbid traditional 4-sided boxes. Use a `surface-container-lowest` fill with a `sm` (0.25rem) corner radius. 
*   **Focus State:** Instead of a heavy border, use a 2px outer "glow" using the `primary` color at 30% opacity.

### Chips & Badges
*   Use `tertiary_container` for neutral status and `primary_container` for active selection. All chips must use `full` (pill) roundedness to contrast against the `xl` roundedness of cards.

### Cards & Lists
*   **The "No Divider" Rule:** In patient lists or data tables, forbid horizontal lines. Use vertical white space (16px - 24px) or alternating subtle background shifts (`surface` to `surface-container-low`) to separate rows.

### Additional Medical Components
*   **Vital Signs Micro-Charts:** Use `secondary` (medical blue) for stable metrics and `error` for critical alerts, rendered in soft, anti-aliased sparklines.
*   **Pulse Status:** A soft, glowing `primary` dot used in navigation to indicate real-time updates or active sessions.

---

## 6. Do’s and Don’ts

### Do:
*   **Do** use white space as a structural element. If a layout feels crowded, increase the padding rather than adding a divider.
*   **Do** use `on_surface_variant` for secondary text to create a clear "Information Architecture" hierarchy.
*   **Do** apply `backdrop-blur` to all persistent navigation overlays.

### Don’t:
*   **Don’t** use pure black (#000000) for text or shadows; it breaks the "Sanctuary" softness. Use `on_surface` (#2d3338).
*   **Don’t** use "Standard" 4px rounded corners. It feels dated. Stick to the `md` (12px) to `xl` (24px) range.
*   **Don’t** use high-contrast, 100% opaque borders. If you can see the line from a meter away, it’s too heavy.

---

**Director’s Final Note:** 
This design system lives in the "in-between" spaces. It is the transition from one purple hue to another, the blur behind a modal, and the generous breathing room between data points that defines the premium experience. Design with intention; every pixel should feel curated.