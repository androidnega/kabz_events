# 🧪 KABZS EVENT - Phase 10 Testing Guide

**Phase:** Search, Filtering & Advanced Features  
**Date:** October 7, 2025  
**Test Environment:** http://localhost:8000  

---

## 🎯 Testing Overview

This guide provides step-by-step instructions for testing the new search and filtering functionality in KABZS EVENT.

---

## ✅ Pre-Testing Checklist

Before testing, ensure:
- [x] Database has categories (10 seeded)
- [x] Database has verified vendors (at least 5)
- [x] Vendors have services assigned
- [x] Vendors have ratings/reviews
- [x] Server is running: `php artisan serve`
- [x] Assets compiled: `npm run dev` or `npm run build`

---

## 🧪 Test Scenarios

### Test 1: Access Search Page
**Objective:** Verify search page loads correctly

**Steps:**
1. Open browser to `http://localhost:8000`
2. Click "Search" in the navigation bar
3. Or go directly to `http://localhost:8000/search`

**Expected Results:**
✅ Search page loads without errors  
✅ Ghana-themed header visible  
✅ Search bar present  
✅ Three filter dropdowns visible  
✅ Vendor cards displayed (if vendors exist)  
✅ Pagination links shown (if >12 vendors)  
✅ Search tips section visible  

---

### Test 2: Keyword Search
**Objective:** Test basic keyword search functionality

**Steps:**
1. Go to `/search`
2. Type "photo" in the search bar
3. Click "Search" button

**Expected Results:**
✅ Page reloads with results  
✅ URL shows: `/search?q=photo`  
✅ Only vendors with "photo" in business name shown  
✅ Results count displayed  
✅ Search term shown above results  
✅ If no results, empty state message appears  

**Try These Keywords:**
- "event"
- "wedding"
- "catering"
- "decoration"
- "music"

---

### Test 3: Category Filter
**Objective:** Test category filtering

**Steps:**
1. Go to `/search`
2. Select "Photography" from Category dropdown
3. Click "Search"

**Expected Results:**
✅ URL shows: `/search?category=photography`  
✅ Only vendors offering photography services shown  
✅ Category filter dropdown shows "Photography" selected  
✅ Pagination maintains category filter  

**Try These Categories:**
- Wedding Planning
- Catering
- Photography
- Decoration
- Music & DJ

---

### Test 4: Region Filter (Ghana)
**Objective:** Test Ghana regional filtering

**Steps:**
1. Go to `/search`
2. Select "Greater Accra" from Region dropdown
3. Click "Search"

**Expected Results:**
✅ URL shows: `/search?region=Greater+Accra`  
✅ Only vendors with "Greater Accra" in address shown  
✅ Region filter shows "Greater Accra" selected  
✅ If no vendors in region, empty state shown  

**Try These Regions:**
- Greater Accra (most common)
- Ashanti
- Western
- Central
- Northern

---

### Test 5: Sort Options
**Objective:** Test sorting functionality

**Steps:**
1. Go to `/search`
2. Select different sort options:
   - "Top Rated"
   - "Most Recent"
   - "Alphabetical"
3. Click "Search" for each

**Expected Results:**
✅ **Top Rated:** Vendors sorted by rating (highest first)  
✅ **Most Recent:** Newest vendors first  
✅ **Alphabetical:** Vendors sorted by business name A-Z  
✅ Sort selection persists in dropdown  
✅ URL reflects sort parameter  

---

### Test 6: Combined Filters
**Objective:** Test multiple filters together

**Steps:**
1. Go to `/search`
2. Enter keyword: "event"
3. Select category: "Wedding Planning"
4. Select region: "Greater Accra"
5. Select sort: "Top Rated"
6. Click "Search"

**Expected Results:**
✅ URL shows all parameters  
✅ Results match ALL filters (AND logic)  
✅ Only Accra wedding planners with "event" shown  
✅ Sorted by rating  
✅ All filters persist on pagination  
✅ "Clear all filters" link visible  

---

### Test 7: Clear Filters
**Objective:** Test filter reset functionality

**Steps:**
1. Apply multiple filters (keyword, category, region)
2. Click "Clear all filters" link

**Expected Results:**
✅ Redirects to `/search` (no parameters)  
✅ All filters reset to default  
✅ Shows all verified vendors  
✅ Default sort applied (Top Rated)  

---

### Test 8: Pagination
**Objective:** Test pagination with filters

**Steps:**
1. Go to `/search`
2. Apply a filter (e.g., category)
3. Click "Next" or "2" in pagination

**Expected Results:**
✅ URL shows: `/search?category=X&page=2`  
✅ Shows vendors 13-24  
✅ Results count updates  
✅ Category filter maintained  
✅ Back button works  
✅ Page numbers clickable  

---

### Test 9: Empty Results
**Objective:** Test empty state handling

**Steps:**
1. Go to `/search`
2. Search for: "xyzabc123" (non-existent)
3. Or select uncommon category + region combo

**Expected Results:**
✅ No PHP errors  
✅ Empty state message shown  
✅ Search tips still visible  
✅ "View All Vendors" button present  
✅ Ghana-friendly messaging  

---

### Test 10: Homepage Integration
**Objective:** Test search integration on homepage

**Steps:**
1. Go to `http://localhost:8000` (homepage)
2. Use the hero search bar
3. Type "photography" and submit
4. Click on a category card (e.g., "Catering")

**Expected Results:**
✅ Hero search redirects to `/search?q=photography`  
✅ Shows photography vendors  
✅ Category card redirects to `/search?category=catering`  
✅ Shows catering vendors  
✅ Navigation maintains site-wide consistency  

---

### Test 11: Navigation Links
**Objective:** Test search links in navigation

**Steps:**
1. Go to any page
2. Click "Search" in main navigation (desktop)
3. On mobile, open menu and click "Search"

**Expected Results:**
✅ Desktop: Search link visible in navbar  
✅ Mobile: Search link in mobile menu  
✅ Both redirect to `/search`  
✅ Active state styling (if implemented)  

---

### Test 12: Vendor Card Display
**Objective:** Test vendor card component

**Steps:**
1. Go to `/search`
2. Examine vendor cards

**Expected Results:**
✅ Business name displayed  
✅ Verified badge (✓) for verified vendors  
✅ Category badges shown (up to 3)  
✅ Location icon + address  
✅ Star rating system (yellow stars)  
✅ Review count in parentheses  
✅ Price range in GH₵ format  
✅ "View Profile" button  
✅ Hover effects work  
✅ Cards responsive on mobile  

---

### Test 13: Mobile Responsiveness
**Objective:** Test mobile experience

**Steps:**
1. Open `/search` in browser
2. Use DevTools (F12) → Toggle device toolbar
3. Test at these widths:
   - 375px (iPhone)
   - 768px (iPad)
   - 1024px (Desktop)

**Expected Results:**
✅ **375px:** 1 vendor card per row  
✅ **375px:** Filters stack vertically  
✅ **768px:** 2 vendor cards per row  
✅ **1024px:** 3 vendor cards per row  
✅ Search bar responsive  
✅ Buttons touch-friendly (min 44px)  
✅ No horizontal scrolling  
✅ Text readable on small screens  

---

### Test 14: Performance
**Objective:** Test search performance

**Steps:**
1. Go to `/search`
2. Apply filters
3. Check browser Network tab (F12)

**Expected Results:**
✅ Page load < 500ms (local)  
✅ Filter submission < 200ms  
✅ No N+1 queries (check Laravel Debugbar)  
✅ Pagination fast (< 100ms)  
✅ No console errors  

---

### Test 15: URL Query String
**Objective:** Test URL parameter handling

**Steps:**
1. Manually type URLs with parameters:
   - `/search?q=wedding`
   - `/search?category=photography&region=Accra`
   - `/search?sort=name&page=2`
2. Hit Enter

**Expected Results:**
✅ Page loads with filters applied  
✅ Dropdowns show correct selections  
✅ Search bar shows keyword  
✅ Results match URL parameters  
✅ Invalid params handled gracefully  

---

## 🐛 Common Issues & Solutions

### Issue 1: "No vendors found" on first load
**Cause:** No verified vendors in database  
**Solution:** Create and verify vendors, or adjust query in SearchController

### Issue 2: Category filter not working
**Cause:** Vendors don't have services assigned  
**Solution:** Assign services to vendors via vendor dashboard

### Issue 3: Region filter returns nothing
**Cause:** Vendor addresses don't match region names  
**Solution:** Update vendor addresses to include Ghana region names

### Issue 4: Rating sort not working
**Cause:** `rating_cached` not updated  
**Solution:** Submit reviews to update cached ratings

### Issue 5: Pagination losing filters
**Cause:** `withQueryString()` not working  
**Solution:** Ensure Laravel 10+ and check Paginator config

---

## ✅ Sign-Off Checklist

After testing, verify:
- [ ] All 15 test scenarios pass
- [ ] No PHP errors in any scenario
- [ ] Mobile responsive (375px, 768px, 1024px)
- [ ] Ghana regions display correctly
- [ ] GH₵ currency formatting correct
- [ ] Verified badges appear
- [ ] Star ratings display properly
- [ ] Pagination works with all filters
- [ ] Empty state handles gracefully
- [ ] Performance acceptable (<500ms)
- [ ] No console errors
- [ ] Links work site-wide
- [ ] Search tips readable
- [ ] Vendor cards hover correctly
- [ ] Filter combinations work

---

## 📊 Test Results Template

```
Date: _______________
Tester: _______________

Test 1: Access Search Page       [PASS / FAIL]
Test 2: Keyword Search            [PASS / FAIL]
Test 3: Category Filter           [PASS / FAIL]
Test 4: Region Filter             [PASS / FAIL]
Test 5: Sort Options              [PASS / FAIL]
Test 6: Combined Filters          [PASS / FAIL]
Test 7: Clear Filters             [PASS / FAIL]
Test 8: Pagination                [PASS / FAIL]
Test 9: Empty Results             [PASS / FAIL]
Test 10: Homepage Integration     [PASS / FAIL]
Test 11: Navigation Links         [PASS / FAIL]
Test 12: Vendor Card Display      [PASS / FAIL]
Test 13: Mobile Responsiveness    [PASS / FAIL]
Test 14: Performance              [PASS / FAIL]
Test 15: URL Query String         [PASS / FAIL]

Overall Status: [PASS / FAIL]
Notes: _________________________________
```

---

## 🎯 Next Steps After Testing

1. **If all tests pass:**
   - Mark Phase 10 as 100% complete
   - Update project status to 85%
   - Move to Phase 11 or complete Phase 9

2. **If tests fail:**
   - Document failures
   - Fix issues one by one
   - Re-test after fixes
   - Update documentation

3. **Optional enhancements:**
   - Add search autocomplete
   - Implement distance-based search
   - Add price range slider
   - Include vendor availability

---

**Happy Testing! 🧪**

Your KABZS EVENT search system is ready to help Ghanaians find the perfect event vendors! 🇬🇭🎉

