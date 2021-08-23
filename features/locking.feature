Feature: Session Locking
  Sessions are locked while open to avoid race conditions or corruption of the data.
  Sessions may be read and closed immediately to minimize blocking.
  Writes to closed sessions will be discarded.
