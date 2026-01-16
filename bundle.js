#!/usr/bin/env node

const fs = require('fs');
const path = require('path');

const EXCLUDE_DIRS = new Set(['node_modules', '.git', 'tests', 'storage', 'public', '.env']);
const OUTPUT_FILE = path.join(__dirname, 'quill.phar');

function getAllPhpFiles(dir, files = []) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });

  for (const entry of entries) {
    if (EXCLUDE_DIRS.has(entry.name)) continue;

    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      getAllPhpFiles(fullPath, files);
    } else if (entry.name.endsWith('.php')) {
      files.push(fullPath);
    }
  }

  return files;
}

function sortPhpFiles(files) {
  // Priority order: bootstrap, config, src, app
  const priorities = {
    bootstrap: 0,
    config: 1,
    src: 2,
    app: 3,
  };

  return files.sort((a, b) => {
    const getPriority = (file) => {
      for (const [key, priority] of Object.entries(priorities)) {
        if (file.includes(key)) return priority;
      }
      return 999;
    };

    const priorityA = getPriority(a);
    const priorityB = getPriority(b);

    if (priorityA !== priorityB) return priorityA - priorityB;
    return a.localeCompare(b);
  });
}

function stripPhpTags(content) {
  // Remove opening <?php tag
  content = content.replace(/^<\?php\s+/, '');
  // Remove closing ?> tag
  content = content.replace(/\s*\?>$/, '');
  return content.trim();
}

function bundlePhpFiles() {
  console.log('🔍 Scanning for PHP files...');
  let phpFiles = getAllPhpFiles(__dirname);
  phpFiles = phpFiles.filter(f => !f.includes('bundle.js'));
  
  if (phpFiles.length === 0) {
    console.error('❌ No PHP files found!');
    process.exit(1);
  }

  console.log(`📦 Found ${phpFiles.length} PHP files`);

  phpFiles = sortPhpFiles(phpFiles);

  console.log('🔗 Bundling files...');

  const header = `<?php
/**
 * Quill Framework - Bundled Distribution
 * Generated: ${new Date().toISOString()}
 * Total Files: ${phpFiles.length}
 */

`;

  let bundledContent = header;
  const fileMap = [];

  for (const file of phpFiles) {
    const relativePath = path.relative(__dirname, file);
    let content = fs.readFileSync(file, 'utf-8');
    content = stripPhpTags(content);

    // Add separator comment
    bundledContent += `\n\n// ============================================\n`;
    bundledContent += `// File: ${relativePath}\n`;
    bundledContent += `// ============================================\n\n`;
    bundledContent += content;

    fileMap.push(relativePath);
  }

  bundledContent += `\n\n// ============================================\n`;
  bundledContent += `// Bundled files (${fileMap.length} total)\n`;
  bundledContent += `// ============================================\n`;
  bundledContent += `// ` + fileMap.join('\n// ');

  fs.writeFileSync(OUTPUT_FILE, bundledContent, 'utf-8');

  const stats = fs.statSync(OUTPUT_FILE);
  const sizeKb = (stats.size / 1024).toFixed(2);

  console.log(`✅ Bundle complete!`);
  console.log(`📄 Output: ${OUTPUT_FILE}`);
  console.log(`📊 Size: ${sizeKb} KB`);
  console.log(`📦 Files: ${phpFiles.length}`);
}

bundlePhpFiles();
