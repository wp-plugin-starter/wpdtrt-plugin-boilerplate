/**
 * File: gulpfile.babel.js
 *
 * Gulp build tasks.
 *
 * Note:
 * - See package.json for scripts, which can be run with:
 *   --- Text
 *   yarn run scriptname
 *   ---
 */

import { series } from 'gulp';

// internal modules
import compile from './gulp-modules/compile';
import dependencies from './gulp-modules/dependencies';
import documentation from './gulp-modules/documentation';
import { TRAVIS } from './gulp-modules/env';
import lint from './gulp-modules/lint';
import release from './gulp-modules/release';
import tests from './gulp-modules/test';
import version from './gulp-modules/version';
import watch from './gulp-modules/watch';

// export combo tasks
export const buildTravis = series(
  dependencies,
  lint,
  compile,
  version,
  documentation,
  tests,
  release
);

export const buildDev = series(
  dependencies,
  lint,
  compile,
  version,
  documentation,
  tests
);

export { compile as compile };
export { dependencies as dependencies };
export { documentation as documentation };
export { lint as lint };
export { release as release };
export { tests as tests };
export { version as version };
export { watch as watch };

/*
 * Export the default task
 *
 * Example:
 * --- bash
 * gulp
 * ---
 */

export default ( TRAVIS ? buildTravis : series( buildDev, watch ) );
