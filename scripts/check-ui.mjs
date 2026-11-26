/**
 * UI ルールチェック
 *
 *   npm run check:ui           … ルール違反を一覧にする（直さない）
 *   npm run fix:ui             … 機械的に直せるもの（Tailwind の色名）をチームの色名に書き換える
 *   npm run check:ui -- <パス>  … 特定のファイル・フォルダだけ調べる
 *
 * 目的
 *  1. 見た目を部品（components/）と CSS（resources/css）だけに任せる
 *     → 後から共通ファイルを直しても、全員の画面に正しく反映される
 *  2. ガイドより前に作られた画面・後から合流した画面を、チームの書き方にそろえる
 *
 * 対象：resources/views の .blade.php（components/・vendor/・Laravel 標準の welcome は除く）
 */
import { readFileSync, writeFileSync, readdirSync, statSync } from 'node:fs';
import { join, relative } from 'node:path';

const args = process.argv.slice(2);
const FIX = args.includes('--fix');
const TARGET = args.find((a) => !a.startsWith('--')) ?? 'resources/views';
const SKIP = ['components', 'vendor', 'welcome.blade.php'];

// ─────────────────────────────────────────────
//  Tailwind の色名 → いちばん近いチームの色名（theme.css で決めた色の系統に合わせる）
// ─────────────────────────────────────────────
const FAMILY = {
    primary: ['blue', 'sky', 'indigo', 'cyan', 'violet', 'purple', 'fuchsia'],
    success: ['green', 'emerald', 'teal', 'lime'],
    danger: ['red', 'rose', 'pink'],
    warning: ['yellow', 'amber', 'orange'],
    gray: ['slate', 'gray', 'zinc', 'neutral', 'stone'],
};
const familyOf = Object.fromEntries(Object.entries(FAMILY).flatMap(([k, list]) => list.map((f) => [f, k])));
const PALETTE = Object.values(FAMILY).flat().join('|');
const HAS_DARK = ['primary', 'success'];

/** 標準の色1つ（例: utility=bg, family=blue, shade=700）→ チームの色名 */
function teamColor(utility, family, shade) {
    const team = familyOf[family];
    const n = Number(shade);
    if (team !== 'gray') {
        if (n <= 300) return `${team}-light`;
        if (n <= 600) return team;
        return HAS_DARK.includes(team) ? `${team}-dark` : team;
    }
    if (utility === 'bg') {
        if (n <= 100) return 'page';
        if (n <= 200) return 'panel';
        if (n <= 400) return 'line';
        if (n <= 600) return 'muted';
        return 'ink';
    }
    if (['text', 'fill', 'stroke', 'placeholder', 'decoration', 'caret'].includes(utility)) {
        if (n <= 200) return 'white';
        if (n <= 600) return 'muted';
        return 'ink';
    }
    // border / ring / divide / outline など線の色
    if (n <= 400) return 'line';
    if (n <= 600) return 'muted';
    return 'ink';
}

const COLOR_UTILITY = 'bg|text|border(?:-[xytblrse])?|ring-offset|ring|divide|outline|fill|stroke|accent|caret|decoration|placeholder|from|via|to';
const COLOR_RE = new RegExp(`(?<![\\w-])((?:[\\w-]+:)*)(${COLOR_UTILITY})-(${PALETTE})-(\\d{2,3})(\\/\\d+)?(?![\\w-])`, 'g');

function fixColors(classValue) {
    return classValue.replace(COLOR_RE, (all, variants, utility, family, shade, opacity = '') => {
        const base = utility.startsWith('border') || ['ring', 'ring-offset', 'divide', 'outline'].includes(utility) ? 'border' : utility;
        return `${variants}${utility}-${teamColor(base, family, shade)}${opacity}`;
    });
}

// ─────────────────────────────────────────────
//  ルール  [正規表現, 説明, 対象]  対象: 'class' = class 属性の中 / 'all' = 行全体
// ─────────────────────────────────────────────
const RULES = [
    // 色・数値
    [new RegExp(`(?<![\\w-])(?:[\\w-]+:)*(?:${COLOR_UTILITY})-(?:${PALETTE})-\\d{2,3}`), 'Tailwind の色名（blue-500 など）を直接書かない → チームの色名にする（npm run fix:ui で自動で直せる）', 'class'],
    [/\[[^\]]*\]/, '[ ] 付きのクラスは使わない → 決まったクラスか部品を使う', 'class'],
    [/(?<![\w-])shadow(-|(?![\w-]))/, '影は使わない（枠線は部品が付ける）', 'class'],
    [/\bbg-(gradient|linear|radial|conic)/, 'グラデーションは使わない', 'class'],
    [/(?<![\w-])text-(xl|[3-9]xl)(?![\w-])/, '決まっていない文字サイズ（ガイド 5. の表にあるものだけ使う）', 'class'],
    [/#[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?\b/, '色コードは書かない → チームの色の名前を使う', 'class'],
    [/\bstyle\s*=\s*"/, 'style="..." は使わない → クラスか部品を使う', 'all'],
    [/<style[\s>]/, '<style> は使わない → 部品の追加をチームに相談', 'all'],

    // 枠・部品（後から合流した画面の移行用）
    [/<!DOCTYPE|<html[\s>]/i, '画面の枠を自分で書いている → <x-layout title="..."> で囲む（ガイド 3.）', 'all'],
    [/<h1[\s>]/, '<h1> は書かない（x-layout の title が出す）', 'all'],
    [/<button[\s>]/, '<button> を手作りしている → <x-button>（送信は type="submit"）', 'all'],
    [/<input(?![^>]*type\s*=\s*"(?:hidden|checkbox|radio)")[\s>]/, '<input> を手作りしている → <x-input>', 'all'],
    [/<input[^>]*type\s*=\s*"radio"/, 'ラジオボタンを手作りしている → <x-radio-group>', 'all'],
    [/<input[^>]*type\s*=\s*"checkbox"/, 'チェックボックスを手作りしている → <x-checkbox>', 'all'],
    [/<select[\s>]/, '<select> を手作りしている → <x-select>', 'all'],
    [/<textarea[\s>]/, '<textarea> を手作りしている → <x-textarea>', 'all'],
    [/<table[\s>]/, '<table> を手作りしている → <x-table>', 'all'],
    [/->links\(\)/, '->links() は使わない → <x-pagination :paginator="..." />', 'all'],
];

function walk(path) {
    if (statSync(path).isFile()) return path.endsWith('.blade.php') ? [path] : [];
    return readdirSync(path).flatMap((name) => (SKIP.includes(name) ? [] : walk(join(path, name))));
}

const CLASS_ATTR = /\bclass\s*=\s*"([^"]*)"/g;
const classValues = (line) => [...line.matchAll(CLASS_ATTR)].map((m) => m[1]);

let problems = 0;
let fixed = 0;

for (const file of walk(TARGET)) {
    let source = readFileSync(file, 'utf8');

    if (FIX) {
        const after = source.replace(CLASS_ATTR, (attr, value) => {
            const next = fixColors(value);
            if (next !== value) fixed += value.match(COLOR_RE)?.length ?? 0;
            return attr.replace(value, next);
        });
        if (after !== source) {
            writeFileSync(file, after);
            console.log(`✎ ${relative('.', file)}  Tailwind の色名をチームの色名に書き換えました`);
            source = after;
        }
    }

    const lines = source
        .replace(/\{\{--[\s\S]*?--\}\}/g, (c) => c.replace(/[^\n]/g, ' ')) // Blade コメントは無視（行番号は保つ）
        .split('\n');

    lines.forEach((line, i) => {
        for (const [re, message, scope] of RULES) {
            const targets = scope === 'class' ? classValues(line) : [line];
            if (targets.some((t) => re.test(t))) {
                problems++;
                console.log(`✗ ${relative('.', file)}:${i + 1}  ${message}`);
                console.log(`    ${line.trim().slice(0, 120)}`);
            }
        }
    });
}

if (FIX) {
    console.log(fixed ? `\n${fixed} か所の色名を書き換えました。画面を開いて色を確認してください。` : '\n自動で直せる箇所はありませんでした。');
}
if (problems === 0) {
    console.log('✓ UI ルール違反はありません');
} else {
    console.log(`\n${problems} 件の違反があります（docs/UI_GUIDE.md の「8. やってはいけないこと」「12. 後から合流した画面をそろえる」を参照）`);
    process.exit(1);
}
