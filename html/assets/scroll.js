function scrollability() {
  const container = document.querySelector(".content-container");
  const content = document.getElementById("scrollable");
  const upBtn = document.createElement("button");
  upBtn.classList.add("scroll-button","scroll-up");
  upBtn.innerHTML='<svg viewBox="0 0 24 24"><path style="fill:#ffffff;stroke:#ffffff;fill-opacity:0.7;" d="M12 6l-8 8h16z"/>';
  const downBtn = document.createElement("button");
  downBtn.classList.add("scroll-button","scroll-down");
  downBtn.innerHTML='<svg viewBox="0 0 24 24"><path style="fill:#ffffff;stroke:#ffffff;fill-opacity:0.7;" d="M12 18l8-8H4z"/>';
  container.insertBefore(upBtn, container.children[0]);
  container.appendChild(downBtn);

  if (!content || !container) return;

  let scrollInterval;

  function updateButtons() {
    upBtn.style.display = content.scrollTop > 0 ? "block" : "none";
    downBtn.style.display = (content.scrollTop + content.clientHeight < content.scrollHeight) ? "block" : "none";
  }

  function startScrolling(y) { scrollInterval = setInterval(() => { content.scrollBy({ top: y, behavior: "auto" }); updateButtons(); }, 50); }

  function stopScrolling() { clearInterval(scrollInterval); }

  upBtn.addEventListener("mousedown", () => startScrolling(-10));
  downBtn.addEventListener("mousedown", () => startScrolling(10));
  upBtn.addEventListener("touchstart", () => startScrolling(-10));
  downBtn.addEventListener("touchstart", () => startScrolling(10));
  upBtn.addEventListener("mouseup", stopScrolling);
  downBtn.addEventListener("mouseup", stopScrolling);
  upBtn.addEventListener("mouseleave", stopScrolling);
  downBtn.addEventListener("mouseleave", stopScrolling);
  upBtn.addEventListener("touchend", stopScrolling);
  downBtn.addEventListener("touchend", stopScrolling);
  upBtn.addEventListener("touchcancel", stopScrolling);
  downBtn.addEventListener("touchcancel", stopScrolling);

  content.addEventListener("scroll", updateButtons);

  window.addEventListener("resize", updateButtons);
  updateButtons();
}
