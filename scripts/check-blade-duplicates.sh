#!/bin/bash

# Script to check for potential duplicate content in Blade components
# This helps prevent looping issues caused by duplicated code

echo "🔍 Checking Blade components for potential duplicates..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

ERRORS=0
WARNINGS=0

# Check for files with suspiciously high line counts (>500 lines)
echo ""
echo "📊 Checking for large files (>500 lines)..."
LARGE_FILES=$(find resources/views/components -name "*.blade.php" -exec sh -c 'lines=$(wc -l < "$1"); if [ $lines -gt 500 ]; then echo "$1: $lines lines"; fi' _ {} \;)

if [ -z "$LARGE_FILES" ]; then
    echo -e "${GREEN}✓ No suspiciously large files found${NC}"
else
    echo -e "${YELLOW}⚠ Warning: Found large files:${NC}"
    echo "$LARGE_FILES"
    WARNINGS=$((WARNINGS + 1))
fi

# Check for multiple @props declarations in same file
echo ""
echo "📋 Checking for multiple @props declarations..."
PROPS_DUPLICATES=$(find resources/views/components -name "*.blade.php" -exec sh -c 'count=$(grep -c "^@props" "$1" 2>/dev/null || true); if [ -n "$count" ] && [ "$count" -gt 1 ]; then echo "$1: $count @props declarations"; fi' _ {} \;)

if [ -z "$PROPS_DUPLICATES" ]; then
    echo -e "${GREEN}✓ No duplicate @props declarations${NC}"
else
    echo -e "${RED}✗ Found files with multiple @props:${NC}"
    echo "$PROPS_DUPLICATES"
    ERRORS=$((ERRORS + 1))
fi

# Check for multiple {{ $slot }} in same file
echo ""
echo "🔌 Checking for multiple {{ \$slot }} declarations..."
SLOT_DUPLICATES=$(find resources/views/components -name "*.blade.php" -exec sh -c 'count=$(grep -c "{{ \$slot }}" "$1" 2>/dev/null || true); if [ -n "$count" ] && [ "$count" -gt 1 ]; then echo "$1: $count slot declarations"; fi' _ {} \;)

if [ -z "$SLOT_DUPLICATES" ]; then
    echo -e "${GREEN}✓ No duplicate {{ \$slot }} declarations${NC}"
else
    echo -e "${RED}✗ Found files with multiple {{ \$slot }}:${NC}"
    echo "$SLOT_DUPLICATES"
    ERRORS=$((ERRORS + 1))
fi

# Check for multiple @push('styles') in same file
echo ""
echo "🎨 Checking for multiple @push('styles') declarations..."
PUSH_DUPLICATES=$(find resources/views/components -name "*.blade.php" -exec sh -c 'count=$(grep -c "@push('"'"'styles'"'"')" "$1" 2>/dev/null || true); if [ -n "$count" ] && [ "$count" -gt 1 ]; then echo "$1: $count @push('"'"'styles'"'"') declarations"; fi' _ {} \;)

if [ -z "$PUSH_DUPLICATES" ]; then
    echo -e "${GREEN}✓ No duplicate @push('styles') declarations${NC}"
else
    echo -e "${YELLOW}⚠ Warning: Found files with multiple @push('styles'):${NC}"
    echo "$PUSH_DUPLICATES"
    WARNINGS=$((WARNINGS + 1))
fi

# Summary
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    exit 0
elif [ $ERRORS -gt 0 ]; then
    echo -e "${RED}✗ Found $ERRORS error(s) and $WARNINGS warning(s)${NC}"
    exit 1
else
    echo -e "${YELLOW}⚠ Found $WARNINGS warning(s)${NC}"
    exit 0
fi

