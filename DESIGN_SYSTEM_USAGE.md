# Design System Usage Guide

## ⚠️ Important: Text Formatting Best Practices

### Problem
Custom classes like `.h1`, `.h2`, `.text-display`, `.text-lead`, `.body-text` may not compile correctly in some cases, causing formatting issues where text appears unstyled.

### Solution: Use Tailwind Classes Directly

**❌ DON'T USE:**
```blade
<h2 class="h2 mb-4">Title</h2>
<p class="text-lead">Subtitle</p>
<p class="body-text-lg">Content</p>
```

**✅ DO USE:**
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">Title</h2>
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">Subtitle</p>
<p class="text-lg text-gray-700 leading-relaxed">Content</p>
```

## Typography Reference

### Headings

#### H1 (Display/Page Title)
```blade
<h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 leading-none tracking-tight">
```

#### H2 (Section Title)
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
```

#### H3 (Subsection Title)
```blade
<h3 class="text-2xl md:text-3xl font-semibold text-gray-900 leading-snug">
```

#### H4
```blade
<h4 class="text-xl md:text-2xl font-semibold text-gray-900 leading-snug">
```

#### H5
```blade
<h5 class="text-lg md:text-xl font-semibold text-gray-900 leading-snug">
```

#### H6
```blade
<h6 class="text-base md:text-lg font-semibold text-gray-900 leading-normal">
```

### Body Text

#### Large Body Text
```blade
<p class="text-lg text-gray-700 leading-relaxed">
```

#### Regular Body Text
```blade
<p class="text-base text-gray-700 leading-relaxed">
```

#### Small Body Text
```blade
<p class="text-sm text-gray-600 leading-relaxed">
```

### Special Text Styles

#### Lead Text (Subtitle/Intro)
```blade
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">
```

#### Caption (Date, Meta Info)
```blade
<time class="text-sm text-gray-500 leading-normal">
```

#### Label (Form Labels, Tags)
```blade
<span class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
```

## Color Reference

- **Headings**: `text-gray-900` (darkest)
- **Body Text**: `text-gray-700` (dark)
- **Secondary Text**: `text-gray-600` (medium)
- **Caption/Meta**: `text-gray-500` (light)
- **Links**: `text-emerald-600` with `hover:text-emerald-700`

## Spacing Reference

- **Section Spacing**: `py-16 md:py-20 lg:py-24`
- **Content Spacing**: `space-y-6 md:space-y-8`
- **Heading Margin Bottom**: `mb-4` (h2), `mb-2` (h3-h6)

## Why This Approach?

1. **Reliability**: Tailwind classes are always compiled and work correctly
2. **Explicit**: You can see exactly what styles are applied
3. **Maintainable**: Easy to modify without hunting for custom class definitions
4. **Performance**: No additional CSS processing needed

## Migration Checklist

When creating new components or pages:

- [ ] Use Tailwind classes directly for headings
- [ ] Use Tailwind classes directly for body text
- [ ] Avoid custom classes like `.h1`, `.h2`, `.text-lead`, `.body-text`
- [ ] Always specify text color explicitly (`text-gray-900`, `text-gray-700`, etc.)
- [ ] Test that formatting renders correctly in browser

## Example: Card Component

```blade
<article class="bg-white rounded-lg shadow-md p-6 text-gray-900">
    <time class="text-sm text-gray-500 mb-2 block leading-normal">
        {{ $date }}
    </time>
    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-2 leading-snug">
        <a href="#" class="text-gray-900 hover:text-emerald-600 transition-colors">
            {{ $title }}
        </a>
    </h3>
    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
        {{ $description }}
    </p>
</article>
```

## Questions?

If you encounter formatting issues:
1. Check if custom classes are being used
2. Replace with Tailwind classes directly
3. Always specify text color explicitly
4. Test in browser to verify rendering


## ⚠️ Important: Text Formatting Best Practices

### Problem
Custom classes like `.h1`, `.h2`, `.text-display`, `.text-lead`, `.body-text` may not compile correctly in some cases, causing formatting issues where text appears unstyled.

### Solution: Use Tailwind Classes Directly

**❌ DON'T USE:**
```blade
<h2 class="h2 mb-4">Title</h2>
<p class="text-lead">Subtitle</p>
<p class="body-text-lg">Content</p>
```

**✅ DO USE:**
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">Title</h2>
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">Subtitle</p>
<p class="text-lg text-gray-700 leading-relaxed">Content</p>
```

## Typography Reference

### Headings

#### H1 (Display/Page Title)
```blade
<h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 leading-none tracking-tight">
```

#### H2 (Section Title)
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
```

#### H3 (Subsection Title)
```blade
<h3 class="text-2xl md:text-3xl font-semibold text-gray-900 leading-snug">
```

#### H4
```blade
<h4 class="text-xl md:text-2xl font-semibold text-gray-900 leading-snug">
```

#### H5
```blade
<h5 class="text-lg md:text-xl font-semibold text-gray-900 leading-snug">
```

#### H6
```blade
<h6 class="text-base md:text-lg font-semibold text-gray-900 leading-normal">
```

### Body Text

#### Large Body Text
```blade
<p class="text-lg text-gray-700 leading-relaxed">
```

#### Regular Body Text
```blade
<p class="text-base text-gray-700 leading-relaxed">
```

#### Small Body Text
```blade
<p class="text-sm text-gray-600 leading-relaxed">
```

### Special Text Styles

#### Lead Text (Subtitle/Intro)
```blade
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">
```

#### Caption (Date, Meta Info)
```blade
<time class="text-sm text-gray-500 leading-normal">
```

#### Label (Form Labels, Tags)
```blade
<span class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
```

## Color Reference

- **Headings**: `text-gray-900` (darkest)
- **Body Text**: `text-gray-700` (dark)
- **Secondary Text**: `text-gray-600` (medium)
- **Caption/Meta**: `text-gray-500` (light)
- **Links**: `text-emerald-600` with `hover:text-emerald-700`

## Spacing Reference

- **Section Spacing**: `py-16 md:py-20 lg:py-24`
- **Content Spacing**: `space-y-6 md:space-y-8`
- **Heading Margin Bottom**: `mb-4` (h2), `mb-2` (h3-h6)

## Why This Approach?

1. **Reliability**: Tailwind classes are always compiled and work correctly
2. **Explicit**: You can see exactly what styles are applied
3. **Maintainable**: Easy to modify without hunting for custom class definitions
4. **Performance**: No additional CSS processing needed

## Migration Checklist

When creating new components or pages:

- [ ] Use Tailwind classes directly for headings
- [ ] Use Tailwind classes directly for body text
- [ ] Avoid custom classes like `.h1`, `.h2`, `.text-lead`, `.body-text`
- [ ] Always specify text color explicitly (`text-gray-900`, `text-gray-700`, etc.)
- [ ] Test that formatting renders correctly in browser

## Example: Card Component

```blade
<article class="bg-white rounded-lg shadow-md p-6 text-gray-900">
    <time class="text-sm text-gray-500 mb-2 block leading-normal">
        {{ $date }}
    </time>
    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-2 leading-snug">
        <a href="#" class="text-gray-900 hover:text-emerald-600 transition-colors">
            {{ $title }}
        </a>
    </h3>
    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
        {{ $description }}
    </p>
</article>
```

## Questions?

If you encounter formatting issues:
1. Check if custom classes are being used
2. Replace with Tailwind classes directly
3. Always specify text color explicitly
4. Test in browser to verify rendering


## ⚠️ Important: Text Formatting Best Practices

### Problem
Custom classes like `.h1`, `.h2`, `.text-display`, `.text-lead`, `.body-text` may not compile correctly in some cases, causing formatting issues where text appears unstyled.

### Solution: Use Tailwind Classes Directly

**❌ DON'T USE:**
```blade
<h2 class="h2 mb-4">Title</h2>
<p class="text-lead">Subtitle</p>
<p class="body-text-lg">Content</p>
```

**✅ DO USE:**
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4 leading-tight">Title</h2>
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">Subtitle</p>
<p class="text-lg text-gray-700 leading-relaxed">Content</p>
```

## Typography Reference

### Headings

#### H1 (Display/Page Title)
```blade
<h1 class="text-5xl md:text-6xl lg:text-7xl font-bold text-gray-900 leading-none tracking-tight">
```

#### H2 (Section Title)
```blade
<h2 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 leading-tight">
```

#### H3 (Subsection Title)
```blade
<h3 class="text-2xl md:text-3xl font-semibold text-gray-900 leading-snug">
```

#### H4
```blade
<h4 class="text-xl md:text-2xl font-semibold text-gray-900 leading-snug">
```

#### H5
```blade
<h5 class="text-lg md:text-xl font-semibold text-gray-900 leading-snug">
```

#### H6
```blade
<h6 class="text-base md:text-lg font-semibold text-gray-900 leading-normal">
```

### Body Text

#### Large Body Text
```blade
<p class="text-lg text-gray-700 leading-relaxed">
```

#### Regular Body Text
```blade
<p class="text-base text-gray-700 leading-relaxed">
```

#### Small Body Text
```blade
<p class="text-sm text-gray-600 leading-relaxed">
```

### Special Text Styles

#### Lead Text (Subtitle/Intro)
```blade
<p class="text-lg md:text-xl text-gray-600 leading-relaxed">
```

#### Caption (Date, Meta Info)
```blade
<time class="text-sm text-gray-500 leading-normal">
```

#### Label (Form Labels, Tags)
```blade
<span class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
```

## Color Reference

- **Headings**: `text-gray-900` (darkest)
- **Body Text**: `text-gray-700` (dark)
- **Secondary Text**: `text-gray-600` (medium)
- **Caption/Meta**: `text-gray-500` (light)
- **Links**: `text-emerald-600` with `hover:text-emerald-700`

## Spacing Reference

- **Section Spacing**: `py-16 md:py-20 lg:py-24`
- **Content Spacing**: `space-y-6 md:space-y-8`
- **Heading Margin Bottom**: `mb-4` (h2), `mb-2` (h3-h6)

## Why This Approach?

1. **Reliability**: Tailwind classes are always compiled and work correctly
2. **Explicit**: You can see exactly what styles are applied
3. **Maintainable**: Easy to modify without hunting for custom class definitions
4. **Performance**: No additional CSS processing needed

## Migration Checklist

When creating new components or pages:

- [ ] Use Tailwind classes directly for headings
- [ ] Use Tailwind classes directly for body text
- [ ] Avoid custom classes like `.h1`, `.h2`, `.text-lead`, `.body-text`
- [ ] Always specify text color explicitly (`text-gray-900`, `text-gray-700`, etc.)
- [ ] Test that formatting renders correctly in browser

## Example: Card Component

```blade
<article class="bg-white rounded-lg shadow-md p-6 text-gray-900">
    <time class="text-sm text-gray-500 mb-2 block leading-normal">
        {{ $date }}
    </time>
    <h3 class="text-lg md:text-xl font-semibold text-gray-900 mb-2 leading-snug">
        <a href="#" class="text-gray-900 hover:text-emerald-600 transition-colors">
            {{ $title }}
        </a>
    </h3>
    <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
        {{ $description }}
    </p>
</article>
```

## Questions?

If you encounter formatting issues:
1. Check if custom classes are being used
2. Replace with Tailwind classes directly
3. Always specify text color explicitly
4. Test in browser to verify rendering

